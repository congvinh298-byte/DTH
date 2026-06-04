import React, { useState } from 'react';
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import { getApiErrorMessage } from '../../core/api/client';
import { createOrder } from '../../core/api/store';
import { formatMoney } from '../../core/data/services';
import { getDeviceFingerprint, isValidPhone, normalizePhone } from '../../core/storage/session';
import { colors, commonStyles } from '../../core/theme';
import PrimaryButton from '../../shared/widgets/PrimaryButton';
import StateNotice from '../../shared/widgets/StateNotice';

export default function OrderScreen({ route, navigation, session }) {
  const product = route.params?.product;
  const [customerName, setCustomerName] = useState(session.name || '');
  const [phone, setPhone] = useState(session.phone || '');
  const [address, setAddress] = useState('');
  const [voucher, setVoucher] = useState('');
  const [note, setNote] = useState('');
  const [paymentMethod, setPaymentMethod] = useState('cod');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const submit = async () => {
    const normalizedPhone = normalizePhone(phone);
    if (!product) {
      setError('Không tìm thấy sản phẩm đã chọn.');
      return;
    }
    if (!customerName.trim() || !isValidPhone(normalizedPhone)) {
      setError('Vui lòng nhập tên và số điện thoại hợp lệ.');
      return;
    }
    if (!address.trim()) {
      setError('Vui lòng nhập địa chỉ nhận hàng.');
      return;
    }

    setLoading(true);
    setError('');
    try {
      const deviceFingerprint = await getDeviceFingerprint();
      const result = await createOrder({
        customer_name: customerName.trim(),
        phone: normalizedPhone,
        product_id: Number(product.id) || 0,
        product_name: product.name,
        name: product.name,
        price: product.price,
        type: product.orderType,
        payment_method: paymentMethod,
        voucher_code: voucher.trim(),
        coupon_code: voucher.trim(),
        note: `Địa chỉ giao hàng: ${address.trim()}${note.trim() ? ` | Ghi chú: ${note.trim()}` : ''}`,
        device_fingerprint: deviceFingerprint,
      });

      Alert.alert(
        'Đặt hàng thành công',
        `Mã đơn: ${result.order_code}\nTổng tiền: ${formatMoney(result.total_price)}`,
        [{ text: 'Về kho sản phẩm', onPress: () => navigation.navigate('Store') }],
      );
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message || 'Không tạo được đơn hàng.'));
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        <View style={styles.productSummary}>
          <Text style={styles.category}>{product?.category || 'Sản phẩm'}</Text>
          <Text style={styles.productName}>{product?.name || 'Chưa chọn sản phẩm'}</Text>
          <Text style={styles.price}>{formatMoney(product?.price)}</Text>
        </View>

        <View style={styles.form}>
          <Text style={styles.label}>Tên khách</Text>
          <TextInput
            onChangeText={setCustomerName}
            placeholder="Nhập tên khách"
            style={commonStyles.input}
            value={customerName}
          />

          <Text style={styles.label}>Số điện thoại</Text>
          <TextInput
            keyboardType="phone-pad"
            maxLength={15}
            onChangeText={(value) => setPhone(normalizePhone(value))}
            placeholder="09xxxxxxxx"
            style={commonStyles.input}
            value={phone}
          />

          <Text style={styles.label}>Địa chỉ nhận hàng</Text>
          <TextInput
            multiline
            onChangeText={setAddress}
            placeholder="Nhập địa chỉ giao hàng"
            style={[commonStyles.input, styles.multiline]}
            textAlignVertical="top"
            value={address}
          />

          <Text style={styles.label}>Mã khuyến mãi</Text>
          <TextInput
            autoCapitalize="characters"
            onChangeText={setVoucher}
            placeholder="Nhập nếu có"
            style={commonStyles.input}
            value={voucher}
          />

          <Text style={styles.label}>Phương thức thanh toán</Text>
          <View style={styles.paymentRow}>
            {[
              { id: 'cod', label: 'Thanh toán khi nhận' },
              { id: 'bank', label: 'Chuyển khoản' },
            ].map((method) => (
              <Pressable
                key={method.id}
                onPress={() => setPaymentMethod(method.id)}
                style={[
                  styles.paymentOption,
                  paymentMethod === method.id && styles.paymentOptionActive,
                ]}
              >
                <Text
                  style={[
                    styles.paymentText,
                    paymentMethod === method.id && styles.paymentTextActive,
                  ]}
                >
                  {method.label}
                </Text>
              </Pressable>
            ))}
          </View>

          <Text style={styles.label}>Ghi chú</Text>
          <TextInput
            multiline
            onChangeText={setNote}
            placeholder="Ghi chú thêm cho đơn hàng"
            style={[commonStyles.input, styles.multiline]}
            textAlignVertical="top"
            value={note}
          />

          {error ? <StateNotice type="error">{error}</StateNotice> : null}
          <PrimaryButton label="Xác nhận đặt hàng" loading={loading} onPress={submit} />
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  content: {
    padding: 14,
    gap: 12,
  },
  productSummary: {
    ...commonStyles.section,
    gap: 6,
  },
  category: {
    color: colors.muted,
    fontSize: 13,
    fontWeight: '700',
  },
  productName: {
    color: colors.text,
    fontSize: 20,
    fontWeight: '800',
  },
  price: {
    color: colors.brand,
    fontSize: 18,
    fontWeight: '800',
  },
  form: {
    ...commonStyles.section,
    gap: 10,
  },
  label: {
    color: colors.text,
    fontSize: 14,
    fontWeight: '700',
    marginTop: 4,
  },
  multiline: {
    minHeight: 72,
  },
  paymentRow: {
    flexDirection: 'row',
    gap: 8,
  },
  paymentOption: {
    flex: 1,
    minHeight: 48,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    backgroundColor: colors.surface,
    padding: 8,
  },
  paymentOptionActive: {
    borderColor: colors.brand,
    backgroundColor: '#fff7f7',
  },
  paymentText: {
    color: colors.muted,
    fontSize: 13,
    fontWeight: '700',
    textAlign: 'center',
  },
  paymentTextActive: {
    color: colors.brand,
  },
});
