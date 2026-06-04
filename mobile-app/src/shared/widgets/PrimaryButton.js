import React from 'react';
import { ActivityIndicator, Pressable, StyleSheet, Text } from 'react-native';
import { colors } from '../../core/theme';

export default function PrimaryButton({
  label,
  onPress,
  loading = false,
  disabled = false,
  variant = 'brand',
  style,
}) {
  const isDisabled = disabled || loading;
  return (
    <Pressable
      accessibilityRole="button"
      disabled={isDisabled}
      onPress={onPress}
      style={({ pressed }) => [
        styles.button,
        variant === 'dark' && styles.dark,
        variant === 'outline' && styles.outline,
        isDisabled && styles.disabled,
        pressed && !isDisabled && styles.pressed,
        style,
      ]}
    >
      {loading ? (
        <ActivityIndicator color={variant === 'outline' ? colors.brand : '#ffffff'} />
      ) : (
        <Text style={[styles.label, variant === 'outline' && styles.outlineLabel]}>{label}</Text>
      )}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  button: {
    minHeight: 48,
    borderRadius: 8,
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: colors.brand,
    alignItems: 'center',
    justifyContent: 'center',
  },
  dark: {
    backgroundColor: colors.dark,
  },
  outline: {
    backgroundColor: colors.surface,
    borderWidth: 1,
    borderColor: colors.brand,
  },
  disabled: {
    opacity: 0.55,
  },
  pressed: {
    opacity: 0.82,
  },
  label: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '700',
  },
  outlineLabel: {
    color: colors.brand,
  },
});
