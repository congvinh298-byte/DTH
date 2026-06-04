export const colors = {
  background: '#f4f5f7',
  surface: '#ffffff',
  text: '#111827',
  muted: '#667085',
  border: '#dfe3e8',
  brand: '#dc2626',
  brandDark: '#b91c1c',
  dark: '#111827',
  success: '#047857',
  successSoft: '#ecfdf3',
  warning: '#9a3412',
  warningSoft: '#fff7ed',
  dangerSoft: '#fff1f2',
};

export const commonStyles = {
  screen: {
    flex: 1,
    backgroundColor: colors.background,
  },
  section: {
    backgroundColor: colors.surface,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    padding: 16,
  },
  input: {
    minHeight: 48,
    backgroundColor: colors.surface,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    color: colors.text,
    fontSize: 16,
  },
};
