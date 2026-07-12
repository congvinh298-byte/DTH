import React, { useState, useEffect } from 'react';
import { Page, Header, Box, Text, Spinner, useSnackbar, Tabs } from 'zmp-ui';
import { openChat } from 'zmp-sdk/apis';

const CatalogPage = () => {
  const { openSnackbar } = useSnackbar();
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [category, setCategory] = useState('dienmay'); // 'dienmay' or '3d'

  useEffect(() => {
    fetchProducts(category);
  }, [category]);

  const fetchProducts = async (cat) => {
    setLoading(true);
    try {
      const res = await fetch(`https://dienmayhieu.com/api/products.php?category=${cat}`);
      const json = await res.json();
      if (json.status === 'success') {
        setProducts(json.data || []);
      }
    } catch (error) {
      openSnackbar({ type: 'error', text: 'Lỗi tải dữ liệu sản phẩm.' });
    } finally {
      setLoading(false);
    }
  };

  const handleBuy = (productName) => {
    openChat({
      type: 'oa',
      id: '959293661751808012',
      message: `Tôi muốn mua sản phẩm: ${productName}. Vui lòng tư vấn cho tôi.`
    });
  };

  return (
    <Page className="page-container">
      <Header title="Cửa Hàng Điện Máy" className="zmp-bg-primary" textColor="white" />
      
      <Tabs activeKey={category} onChange={(key) => setCategory(key)}>
        <Tabs.Tab key="dienmay" label="Điện Máy Gia Dụng" />
        <Tabs.Tab key="3d" label="Mô Hình 3D" />
      </Tabs>

      <Box p={4}>
        {loading ? (
          <div style={{ textAlign: 'center', padding: '40px' }}>
            <Spinner visible />
          </div>
        ) : products.length === 0 ? (
          <div style={{ textAlign: 'center', padding: '40px', color: '#64748b' }}>
            Hiện chưa có sản phẩm nào trong danh mục này.
          </div>
        ) : (
          <div className="product-grid">
            {products.map(p => (
              <div key={p.id} className="product-card">
                <img src={p.image || 'https://via.placeholder.com/150'} alt={p.name} className="product-img" />
                <div className="product-info">
                  <div className="product-name">{p.name}</div>
                  <div className="product-price">{Number(p.price).toLocaleString()}đ</div>
                  <button className="btn-accent" onClick={() => handleBuy(p.name)}>Mua Ngay</button>
                </div>
              </div>
            ))}
          </div>
        )}
      </Box>
    </Page>
  );
};

export default CatalogPage;
