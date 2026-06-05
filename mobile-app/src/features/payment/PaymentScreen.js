import React, { useState, useEffect } from 'react';
import { Alert, StyleSheet, Text, Pressable, View } from 'react-native';
import api from '../../core/api/client';
import { clearActiveBooking } from '../../core/storage/session';
import { colors, commonStyles } from '../../core/theme';
import PrimaryButton from '../../shared/widgets/PrimaryButton';

export default function PaymentScreen({ route, navigation }) {
  const { booking, worker, amount } = route.params || {};
  const [rating, setRating] = useState(0);
  const [loading, setLoading] = useState(false);

  const [timeLeft, setTimeLeft] = useState(600); // 10 minutes

  useEffect(() => {
    if (timeLeft <= 0) {
      // Auto submit 5 stars
      finish(5, true);
      return;
    }
    const timer = setInterval(() => setTimeLeft(prev => prev - 1), 1000);
    return () => clearInterval(timer);
  }, [timeLeft]);

  const finish = async (forcedRating = null, isAuto = false) => {
    const finalRating = forcedRating || rating;
    if (finalRating < 1) {
      if (!isAuto) Alert.alert('Chưa đánh giá', 'Vui lòng chọn từ 1 đến 5 sao.');
      return;
    }

    setLoading(true);
    try {
      if (worker?.id) {
        await api.post('?action=app_submit_rating', { target_type: 'worker', target_id: worker.id, stars: finalRating });
      }
      await clearActiveBooking();
      if (!isAuto) {
        Alert.alert('Cảm ơn khách hàng', 'Đánh giá đã được gửi thành công.');
      }
      navigation.popToTop();
    } catch {
      if (!isAuto) Alert.alert('Chưa gửi được đánh giá', 'Vui lòng thử lại sau.');
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
        <Text style={styles.label}>Đánh giá chất lượng phục vụ (Còn {Math.floor(timeLeft/60)}:{(timeLeft%60).toString().padStart(2,'0')})</Text>
        <View style={styles.stars}>
          {[1, 2, 3, 4, 5].map((star) => (
            <Pressable key={star} onPress={() => setRating(star)}>
              <Text style={[styles.star, star <= rating && styles.starActive]}>★</Text>
            </Pressable>
          ))}
        </View>
        <PrimaryButton label="Gửi đánh giá và hoàn tất" loading={loading} onPress={() => finish(null, false)} />
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
