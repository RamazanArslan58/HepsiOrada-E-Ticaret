# HepsiOrada - E-Ticaret Projesi 🛒

Bu proje, PHP ve MySQL kullanılarak geliştirilmiş, dinamik ürün listeleme, kategori bazlı filtreleme, arama ve kullanıcı yönetim sistemine sahip bir e-ticaret web uygulaması simülasyonudur.

## 🚀 Özellikler
* **Dinamik Ürün Yönetimi:** Ürünlerin fiyata, kategoriye ve isme göre anlık SQL sorgularıyla filtrelenmesi.
* **Kullanıcı Sistemi:** Güvenli üye kayıt (`password_verify` ile şifre hashlama) ve session tabanlı giriş/çıkış kontrolü.
* **Sepet Yönetimi:** Ürünlerin hızlı bir şekilde sepete eklenmesi (`sepet-islem.php`).
* **Soru-Cevap Sistemi:** Ürün detay sayfasında kullanıcıların soru sorabilmesi ve satıcı yanıtları alanı.

## 🛠️ Kullanılan Teknolojiler
* **Backend:** PHP 8.x, PDO (Güvenli SQL sorguları için Prepared Statements)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3 (Inline & External Styles)

## 🔧 Kurulum
1. Bu repoyu bilgisayarınıza klonlayın.
2. `veritabani.sql` dosyasını MySQL veritabanınıza içe aktarın.
3. `baglan-ornek.php` dosyasının adını `baglan.php` olarak değiştirin ve kendi veritabanı bilgilerinizi girin.
