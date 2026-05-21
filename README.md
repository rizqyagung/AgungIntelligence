# 🤖 Agung Intelligence (AgungAI) v1.1

AgungAI adalah aplikasi web asisten cerdas berbasis kecerdasan buatan (AI) yang mengintegrasikan **Google Gemini API (v1beta)** sebagai engine pemikir, dengan backend **PHP murni** dan penyimpanan riwayat chat menggunakan database **MySQL**. 

Project ini dibangun di atas lingkungan server **Ubuntu Linux** dengan server web **Apache2**.

## 🚀 Fitur Utama
- **Multi-Model Selector**: Menggunakan model cerdas `gemini-2.5-pro` dan model cepat `gemini-2.5-flash`.
- **Dynamic Sidebar History**: Riwayat chat tersimpan secara *real-time* di database MySQL dan ditampilkan secara dinamis menggunakan AJAX Fetch API.
- **Responsive UI**: Tampilan antarmuka modern dan bersih menggunakan Tailwind CSS dengan dukungan mode gelap (Dark Mode).

## 🛠️ Stack Teknologi & Infrastruktur
- **Frontend**: HTML5, Tailwind CSS, JavaScript (ES6+, Fetch API)
- **Backend**: PHP 8.x (cURL Extension, PDO MySQL)
- **Database**: MySQL Server
- **OS & Web Server**: Ubuntu Linux, Apache2

## 📦 Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone [https://github.com/username-kamu/agungAI.git](https://github.com/username-kamu/agungAI.git)
   mv agungAI /var/www/html/
