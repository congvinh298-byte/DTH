import React, { useState } from 'react';
import { Alert, StyleSheet, Text, Pressable, View } from 'react-native';
import { submitRating } from '../../core/api/services';
import { clearActiveBooking } from '../../core/storage/session';
import { colors, commonStyles } from '../../core/theme';
import PrimaryButton from '../../shared/widgets/PrimaryButton';

export default function PaymentScreen({ route, navigation }) {
  const { booking, worker, amount } = route.params || {};
  const [rating, setRating] = useState(0);
  const [loading, setLoading] = useState(false);

  const finish = async () => {
    if (rating < 1) {
      Alert.alert('Chưa đánh giá', 'Vui lòng chọn từ 1 đến 5 sao.');
      return;
    }

    setLoading(true);
    try {
      const synced = await submitRating(booking?.id, rating);
      await clearActiveBooking();
      Alert.alert(
        'Cảm ơn khách hàng',
        synced
          ? 'Đánh giá đã được gửi thành công.'
          : 'Đã hoàn tất. Máy chủ chưa bật đồng bộ đánh giá.',
      );
      navigation.popToTop();
    } catch {
      Alert.alert('Chưa gửi được đánh giá', 'Vui lòng thử lại sau.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.panel}>
        <Text style={styles.title}>Công việc đã hoàn thành</Text>
        <Text style={styles.text}>Mã yêu cầu: #{booking?.id || ''}</Text>
        <Text style={styles.text}>Thợ phụ trách: {worker?.name || 'Đang cập nhật'}</Text>
        <Text style={styles.amount}>Giá khách: {amount || 'Theo báo giá đã xác nhận'}</Text>
        <Text style={styles.label}>Đánh giá chất lượng phục vụ</Text>
        <View style={styles.stars}>
          {[1, 2, 3, 4, 5].map((star) => (
            <Pressable key={star} onPress={() => setRating(star)}>
              <Text style={[styles.star, star <= rating && styles.starActive]}>★</Text>
            </Pressable>
          ))}
        </View>
        <PrimaryButton label="Gửi đánh giá và hoàn tất" loading={loading} onPress={finish} />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    backgroundColor: colors.background,
    padding: 16,
  },
  panel: {
    ...commonStyles.section,
    gap: 12,
  },
  title: {
    color: colors.success,
    fontSize: 22,
    fontWeight: '800',
  },
  text: {
    color: colors.text,
    fontSize: 15,
  },
  amount: {
    color: colors.brand,
    fontSize: 17,
    fontWeight: '800',
  },
  label: {
    color: colors.text,
    fontSize: 15,
    fontWeight: '700',
    marginTop: 8,
  },
  stars: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  star: {
    color: '#d1d5db',
    fontSize: 42,
  },
  starActive: {
    color: '#f59e0b',
  },
});
