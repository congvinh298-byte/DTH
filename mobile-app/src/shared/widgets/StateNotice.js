import React from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { colors } from '../../core/theme';

export default function StateNotice({ type = 'warning', children, style }) {
  return (
    <View
      style={[
        styles.notice,
        type === 'success' && styles.success,
        type === 'error' && styles.error,
        style,
      ]}
    >
      <Text
        style={[
          styles.text,
          type === 'success' && styles.successText,
          type === 'error' && styles.errorText,
        ]}
      >
        {children}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  notice: {
    borderWidth: 1,
    borderColor: '#fed7aa',
    borderRadius: 8,
    backgroundColor: colors.warningSoft,
    padding: 12,
  },
  success: {
    borderColor: '#a7f3d0',
    backgroundColor: colors.successSoft,
  },
  error: {
    borderColor: '#fecdd3',
    backgroundColor: colors.dangerSoft,
  },
  text: {
    color: colors.warning,
    fontSize: 14,
    lineHeight: 20,
  },
  successText: {
    color: colors.success,
  },
  errorText: {
    color: colors.brandDark,
  },
});
