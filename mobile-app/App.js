import 'react-native-gesture-handler';
import React, { useEffect, useState } from 'react';
import { ActivityIndicator, StatusBar, StyleSheet, Text, View } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { SafeAreaProvider } from 'react-native-safe-area-context';

import LoginScreen from './src/features/auth/LoginScreen';
import HomeScreen from './src/features/home/HomeScreen';
import MapScreen from './src/features/map/MapScreen';
import StoreLoginScreen from './src/features/store/StoreLoginScreen';
import StoreListScreen from './src/features/store/StoreListScreen';
import StoreMenuScreen from './src/features/store/StoreMenuScreen';
import AssistantScreen from './src/features/assistant/AssistantScreen';
import StoreScreen from './src/features/store/StoreScreen';
import StoreProductsScreen from './src/features/store/StoreProductsScreen';
import OrderScreen from './src/features/store/OrderScreen';
import BookingScreen from './src/features/booking/BookingScreen';
import TrackingScreen from './src/features/tracking/TrackingScreen';
import PaymentScreen from './src/features/payment/PaymentScreen';
import { clearSession, loadSession } from './src/core/storage/session';
import { colors } from './src/core/theme';

const Stack = createStackNavigator();

export default function App() {
  const [session, setSession] = useState(null);
  const [booting, setBooting] = useState(true);

  useEffect(() => {
    loadSession()
      .then((storedSession) => setSession(storedSession.phone ? storedSession : null))
      .catch(() => setSession(null))
      .finally(() => setBooting(false));
  }, []);

  const logout = async () => {
    await clearSession();
    setSession(null);
  };

  if (booting) {
    return (
      <SafeAreaProvider>
        <View style={styles.bootScreen}>
          <ActivityIndicator size="large" color={colors.brand} />
          <Text style={styles.bootText}>Đang khởi động ứng dụng...</Text>
        </View>
      </SafeAreaProvider>
    );
  }

  return (
    <SafeAreaProvider>
      <StatusBar barStyle="dark-content" backgroundColor="#ffffff" />
      <NavigationContainer>
        <Stack.Navigator
          screenOptions={{
            headerStyle: { backgroundColor: colors.brand },
            headerTintColor: '#ffffff',
            headerTitleStyle: { fontWeight: '700' },
            headerBackTitleVisible: false,
          }}
        >
          {session ? (
            <>
              <Stack.Screen name="Home" options={{ title: 'Điện Tử Hiếu' }}>
                {(props) => <HomeScreen {...props} session={session} onLogout={logout} />}
              </Stack.Screen>
              <Stack.Screen name="MapScreen" component={MapScreen} options={{ title: 'Bản Đồ Cửa Hàng' }} />
              <Stack.Screen name="StoreList" component={StoreListScreen} options={{ title: 'Danh Sách Cửa Hàng' }} />
              <Stack.Screen name="StoreMenu" component={StoreMenuScreen} options={{ title: 'Menu Cửa Hàng' }} />
              <Stack.Screen name="StoreLogin" component={StoreLoginScreen} options={{ title: 'Đăng Ký Cửa Hàng' }} />
              <Stack.Screen name="Booking" options={{ title: 'Thông tin gọi thợ' }}>
                {(props) => <BookingScreen {...props} session={session} />}
              </Stack.Screen>
              <Stack.Screen name="Tracking" options={{ title: 'Theo dõi yêu cầu' }}>
                {(props) => <TrackingScreen {...props} />}
              </Stack.Screen>
              <Stack.Screen name="Payment" options={{ title: 'Hoàn thành công việc' }}>
                {(props) => <PaymentScreen {...props} />}
              </Stack.Screen>
              <Stack.Screen name="Assistant" options={{ title: 'Trợ lý báo giá' }}>
                {(props) => <AssistantScreen {...props} />}
              </Stack.Screen>
              <Stack.Screen name="Store" options={{ title: 'Mua hàng' }}>
                {(props) => <StoreScreen {...props} />}
              </Stack.Screen>
              <Stack.Screen name="StoreProducts" options={{ title: 'Quản lý hàng hóa' }}>
                {(props) => <StoreProductsScreen {...props} />}
              </Stack.Screen>
              <Stack.Screen name="Order" options={{ title: 'Xác nhận đặt hàng' }}>
                {(props) => <OrderScreen {...props} session={session} />}
              </Stack.Screen>
            </>
          ) : (
            <>
              <Stack.Screen name="Login" options={{ headerShown: false }}>
                {(props) => <LoginScreen {...props} onAuthenticated={setSession} />}
              </Stack.Screen>
              <Stack.Screen name="StoreProducts" options={{ title: 'Quản lý hàng hóa' }}>
                {(props) => <StoreProductsScreen {...props} />}
              </Stack.Screen>
            </>
          )}
        </Stack.Navigator>
      </NavigationContainer>
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  bootScreen: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.surface,
    gap: 14,
  },
  bootText: {
    color: colors.muted,
    fontSize: 15,
  },
});
