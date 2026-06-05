import React, { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, Linking, ScrollView, StyleSheet, Text, View } from 'react-native';
import { getApiErrorMessage } from '../../core/api/client';
import { loadBookingStatus } from '../../core/api/services';
import { formatMoney } from '../../core/data/services';
import { loadActiveBooking } from '../../core/storage/session';
import { colors, commonStyles } from '../../core/theme';
import PrimaryButton from '../../shared/widgets/PrimaryButton';
import StateNotice from '../../shared/widgets/StateNotice';

export default function TrackingScreen({ route, navigation }) {
  const [booking, setBooking] = useState(route.params?.booking || null);
  const [status, setStatus] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const tablePrice =
    Number(booking?.service?.publicPrice || 0) * Math.max(1, Number(booking?.quantity || 1));

  const checkStatus = useCallback(
    async (activeBooking) => {
      if (!activeBooking?.id) {
        setLoading(false);
        return;
      }

      setLoading(true);
      setError('');
      try {
        const nextStatus = await loadBookingStatus(activeBooking.id);
        setStatus(nextStatus);
        if (nextStatus.supported && nextStatus.status_code === 'completed') {
          navigation.replace('Payment', {
            booking: activeBooking,
            worker: nextStatus.worker,
            amount: nextStatus.amount,
          });
        }
      } catch (requestError) {
        setError(getApiErrorMessage(requestError, 'Không kiểm tra được trạng thái yêu cầu.'));
      } finally {
        setLoading(false);
      }
    },
    [navigation],
  );

  useEffect(() => {
    const start = async () => {
      const activeBooking = booking || (await loadActiveBooking());
      setBooking(activeBooking);
      await checkStatus(activeBooking);
    };
    start();
  }, []);

  const openMap = async () => {
    if (!booking?.coordinates) {
      return;
    }
    const { latitude, longitude } = booking.coordinates;
    await Linking.openURL(
      `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`,
    );
  };

  if (!booking) {
    return (
      <View style={styles.center}>
        <StateNotice type="error">Không tìm thấy yêu cầu đang theo dõi.</StateNotice>
        <PrimaryButton label="Về danh sách dịch vụ" onPress={() => navigation.navigate('Home')} />
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.content}>
      <StateNotice type="success">Yêu cầu đã gửi thành công đến hệ thống điều phối thợ.</StateNotice>

      <View style={styles.summary}>
        <Text style={styles.jobId}>Mã yêu cầu #{booking.id}</Text>
        <Text style={styles.serviceName}>{booking.service?.name}</Text>
        <Text style={styles.meta}>{booking.address}</Text>
        <Text style={styles.price}>
          Giá công khai đã gồm VAT: {formatMoney(tablePrice || booking.result?.final_total)}
        </Text>
      </View>

      <View style={styles.statusBox}>
        <Text style={styles.sectionTitle}>Trạng thái</Text>
        {loading ? <ActivityIndicator color={colors.brand} /> : null}
        <Text style={styles.statusText}>
          {status?.status_text || 'Yêu cầu đang chờ thợ nhận ca'}
        </Text>
        {!status?.supported ? (
          <Text style={styles.meta}>
            Máy chủ chưa bật theo dõi trực tiếp. Thợ sẽ liên hệ theo số điện thoại đã đăng nhập.
          </Text>
        ) : null}
        {status?.worker ? (
          <View>
            <Text style={styles.meta}>
              Thợ nhận ca: {status.worker.name || 'Đang cập nhật'} {status.worker.phone || ''}
            </Text>
            <Text style={styles.ratingText}>
              Đánh giá: ⭐ {Number(status.worker.rating_score || 5.0).toFixed(1)} ({status.worker.rating_count || 0} lượt)
            </Text>
          </View>
        ) : null}
      </View>

      {error ? <StateNotice type="error">{error}</StateNotice> : null}

      <PrimaryButton label="Mở vị trí bằng Google Maps" onPress={openMap} variant="dark" />
      <PrimaryButton
        label="Kiểm tra trạng thái"
        loading={loading}
        onPress={() => checkStatus(booking)}
        variant="outline"
      />
      <PrimaryButton label="Tạo yêu cầu khác" onPress={() => navigation.navigate('Home')} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  content: {
    flexGrow: 1,
    backgroundColor: colors.background,
    padding: 16,
    gap: 12,
  },
  center: {
    flex: 1,
    justifyContent: 'center',
    gap: 12,
    backgroundColor: colors.background,
    padding: 16,
  },
  summary: {
    ...commonStyles.section,
    gap: 8,
  },
  statusBox: {
    ...commonStyles.section,
    gap: 10,
  },
  sectionTitle: {
    color: colors.text,
    fontSize: 16,
    fontWeight: '800',
  },
  jobId: {
    color: colors.brand,
    fontSize: 19,
    fontWeight: '800',
  },
  serviceName: {
    color: colors.text,
    fontSize: 18,
    fontWeight: '800',
  },
  price: {
    color: colors.brand,
    fontSize: 15,
    fontWeight: '700',
  },
  statusText: {
    color: colors.success,
    fontSize: 17,
    fontWeight: '800',
  },
  meta: {
    color: colors.muted,
    fontSize: 14,
    lineHeight: 20,
  },
  ratingText: {
    color: '#f59e0b',
    fontWeight: 'bold',
    fontSize: 15,
    marginTop: 4,
  }
});
