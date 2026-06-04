export const SERVICE_CATALOG = [
  {
    id: 'aircon-cleaning',
    group: 'Thợ điện lạnh',
    name: 'Vệ sinh máy lạnh',
    techBase: 150000,
    publicPrice: 165000,
    note: 'Giá công khai đã gồm VAT.',
  },
  {
    id: 'aircon-install-small',
    group: 'Thợ điện lạnh',
    name: 'Lắp đặt máy lạnh 1HP / 1.5HP',
    techBase: 400000,
    publicPrice: 440000,
    note: 'Chưa gồm vật tư phát sinh.',
  },
  {
    id: 'aircon-install-large',
    group: 'Thợ điện lạnh',
    name: 'Lắp đặt máy lạnh 2HP / 3HP',
    techBase: 500000,
    publicPrice: 550000,
    note: 'Chưa gồm vật tư phát sinh.',
  },
  {
    id: 'ceiling-aircon',
    group: 'Thợ điện lạnh',
    name: 'Máy lạnh âm trần',
    techBase: 0,
    publicPrice: 0,
    note: 'Báo giá sau khi tư vấn.',
  },
  {
    id: 'aircon-repair',
    group: 'Thợ điện lạnh',
    name: 'Sửa chữa điện lạnh',
    techBase: 200000,
    publicPrice: 220000,
    note: 'Linh kiện phát sinh được báo riêng.',
  },
  {
    id: 'tv-mounting',
    group: 'Thợ tivi',
    name: 'Treo tivi',
    techBase: 200000,
    publicPrice: 220000,
    note: 'Chưa gồm khung treo.',
  },
  {
    id: 'water-filter-install',
    group: 'Thợ máy lọc nước',
    name: 'Lắp máy lọc nước',
    techBase: 200000,
    publicPrice: 220000,
    note: 'Phụ kiện phát sinh được báo riêng.',
  },
  {
    id: 'washing-machine-install',
    group: 'Thợ gia dụng',
    name: 'Lắp máy giặt',
    techBase: 200000,
    publicPrice: 220000,
    note: 'Phụ kiện phát sinh được báo riêng.',
  },
  {
    id: 'phone-repair',
    group: 'Thợ điện thoại',
    name: 'Kiểm tra / sửa điện thoại',
    techBase: 200000,
    publicPrice: 220000,
    note: 'Linh kiện phát sinh được báo riêng.',
  },
];

export function formatMoney(value) {
  if (!Number.isFinite(Number(value)) || Number(value) <= 0) {
    return 'Liên hệ';
  }

  return `${Math.round(Number(value)).toLocaleString('vi-VN')} VND`;
}

export function normalizeServerServices(items) {
  if (!Array.isArray(items)) {
    return [];
  }

  return items
    .map((item, index) => {
      const looksLikeService =
        item?.service_name ||
        item?.group ||
        item?.tech_base !== undefined ||
        item?.base !== undefined ||
        item?.public_price !== undefined;
      if (!looksLikeService) {
        return null;
      }

      const name = String(item?.name || item?.service_name || '').trim();
      if (!name) {
        return null;
      }

      const techBase = Number(item?.techBase ?? item?.tech_base ?? item?.base ?? item?.price ?? 0);
      const publicPrice = Number(
        item?.publicPrice ?? item?.public_price ?? item?.customer_price ?? item?.price ?? 0,
      );

      return {
        id: String(item?.id || `server-service-${index}`),
        group: String(item?.group || item?.category || 'Dịch vụ kỹ thuật').trim(),
        name,
        techBase: Number.isFinite(techBase) ? techBase : 0,
        publicPrice: Number.isFinite(publicPrice) ? publicPrice : 0,
        note: String(item?.note || item?.description || 'Giá tham khảo.').trim(),
      };
    })
    .filter(Boolean);
}
