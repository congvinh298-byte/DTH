import { requestApi, getApiErrorMessage } from './client';
import { SERVICE_CATALOG, normalizeServerServices } from '../data/services';

export async function loadServiceCatalog() {
  try {
    const result = await requestApi('app_services');
    const services = normalizeServerServices(result?.data);

    if (result?.status === 'success' && services.length > 0) {
      return { services, source: 'server', warning: '' };
    }
  } catch (error) {
    return {
      services: SERVICE_CATALOG,
      source: 'bundled',
      warning:
        error?.response?.status === 404
          ? ''
          : getApiErrorMessage(error, 'Đang dùng bảng giá đã lưu trong ứng dụng.'),
    };
  }

  return {
    services: SERVICE_CATALOG,
    source: 'bundled',
    warning: 'Máy chủ chưa có bảng giá hợp lệ. Đang dùng bảng giá đã lưu trong ứng dụng.',
  };
}

export async function createBooking(payload) {
  const result = await requestApi('create_job', {
    method: 'post',
    data: payload,
  });

  if (!(result?.status === 'success' || result?.success === true)) {
    throw new Error(result?.message || 'Không thể tạo yêu cầu gọi thợ.');
  }

  return {
    ...result,
    bookingId: result?.job_id || result?.booking_id,
  };
}

export async function loadBookingStatus(bookingId) {
  try {
    const result = await requestApi('app_job_status', {
      params: { booking_id: bookingId },
    });

    if (result?.status !== 'success') {
      throw new Error(result?.message || 'Không đọc được trạng thái yêu cầu.');
    }

    return { supported: true, ...result.data };
  } catch (error) {
    if (error?.response?.status === 404) {
      return {
        supported: false,
        status_code: 'pending',
        status_text: 'Yêu cầu đã được chuyển đến nhóm thợ',
      };
    }

    throw error;
  }
}

export async function submitRating(bookingId, rating) {
  try {
    const result = await requestApi('app_rate_job', {
      method: 'post',
      data: { booking_id: bookingId, rating },
    });
    return result?.status === 'success';
  } catch (error) {
    if (error?.response?.status === 404) {
      return false;
    }
    throw error;
  }
}

export async function askQuoteAssistant({ message, service, address = '' }) {
  const result = await requestApi('gemini_chat', {
    method: 'post',
    data: {
      message,
      service_type: service?.group || '',
      selected_service: service?.name || '',
      public_price: service?.publicPrice || 0,
      address,
    },
  });

  if (result?.status !== 'success') {
    throw new Error(result?.message || 'Trợ lý chưa thể phản hồi lúc này.');
  }

  return String(result?.reply || '').trim();
}
