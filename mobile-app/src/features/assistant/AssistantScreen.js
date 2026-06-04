import React, { useState } from 'react';
import {
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import { askQuoteAssistant } from '../../core/api/services';
import { getApiErrorMessage } from '../../core/api/client';
import { colors, commonStyles } from '../../core/theme';
import PrimaryButton from '../../shared/widgets/PrimaryButton';
import StateNotice from '../../shared/widgets/StateNotice';

export default function AssistantScreen({ route }) {
  const service = route.params?.service;
  const [question, setQuestion] = useState(
    service ? `Tư vấn giúp tôi dịch vụ ${service.name}.` : '',
  );
  const [reply, setReply] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    if (!question.trim()) {
      setError('Vui lòng nhập nội dung cần tư vấn.');
      return;
    }

    setLoading(true);
    setError('');
    try {
      setReply(await askQuoteAssistant({ message: question.trim(), service }));
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, requestError?.message));
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
        <Text style={styles.title}>Tư vấn dịch vụ và báo giá</Text>
        <Text style={styles.description}>
          Mô tả thiết bị, tình trạng và nhu cầu để nhận tư vấn trước khi gọi thợ.
        </Text>
        <TextInput
          multiline
          onChangeText={setQuestion}
          placeholder="Ví dụ: máy lạnh 1HP không lạnh, cần kiểm tra tại Lấp Vò"
          style={[commonStyles.input, styles.input]}
          textAlignVertical="top"
          value={question}
        />
        <PrimaryButton label="Gửi yêu cầu tư vấn" loading={loading} onPress={submit} />
        {error ? <StateNotice type="error">{error}</StateNotice> : null}
        {reply ? (
          <View style={styles.reply}>
            <Text style={styles.replyTitle}>Nội dung tư vấn</Text>
            <Text style={styles.replyText}>{reply}</Text>
          </View>
        ) : null}
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
    padding: 16,
    gap: 14,
  },
  title: {
    color: colors.text,
    fontSize: 22,
    fontWeight: '800',
  },
  description: {
    color: colors.muted,
    fontSize: 14,
    lineHeight: 20,
  },
  input: {
    minHeight: 130,
  },
  reply: {
    ...commonStyles.section,
    gap: 8,
  },
  replyTitle: {
    color: colors.text,
    fontSize: 16,
    fontWeight: '800',
  },
  replyText: {
    color: colors.text,
    fontSize: 15,
    lineHeight: 22,
  },
});
