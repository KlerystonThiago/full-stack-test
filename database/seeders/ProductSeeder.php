<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Placa de Vídeo NVIDIA RTX 5090',
                'description' => 'Placa de Vídeo Gigabyte RTX 5090 AORUS MASTER 32G NVIDIA GeForce, 32GB, GDDR7, RGB, G-Sync, Ray Tracing, DLSS 4, HDR - GV-N5090AORUS M-32GD',
                'price' => 20500.00
            ],
            [
                'name' => 'Processador AMD Ryzen 9 7950X3D',
                'description' => 'Processador AMD Ryzen 9 7950X3D, 16-Core, 32-Threads, 5.7GHz Turbo, Cache 144MB, AM5',
                'price' => 4299.99
            ],
            [
                'name' => 'Processador Intel Core i9-14900KS',
                'description' => 'Processador Intel Core i9-14900KS, 24-Core, 32-Threads, 6.2GHz Turbo, Cache 36MB, LGA1700',
                'price' => 4899.00
            ],
            [
                'name' => 'Placa-Mãe ASUS ROG MAXIMUS Z790 HERO',
                'description' => 'Placa-Mãe ASUS ROG MAXIMUS Z790 HERO, Intel LGA1700, ATX, DDR5, PCIe 5.0, WiFi 6E, RGB',
                'price' => 3799.00
            ],
            [
                'name' => 'Memória RAM Corsair Dominator Titanium 64GB',
                'description' => 'Memória RAM Corsair Dominator Titanium RGB 64GB (2x32GB), DDR5, 6400MHz, CL32, Preta',
                'price' => 2899.00
            ],
            [
                'name' => 'SSD Samsung 990 PRO 4TB NVMe',
                'description' => 'SSD Samsung 990 PRO 4TB, M.2 NVMe, PCIe 4.0, Leitura 7450MB/s, Gravação 6900MB/s',
                'price' => 2499.00
            ],
            [
                'name' => 'Water Cooler Corsair iCUE H150i Elite LCD XT',
                'description' => 'Water Cooler Corsair iCUE H150i Elite LCD XT, 360mm, Display LCD 2.1", RGB, Intel/AMD',
                'price' => 1899.00
            ],
            [
                'name' => 'Gabinete Lian Li O11 Dynamic EVO RGB',
                'description' => 'Gabinete Lian Li O11 Dynamic EVO RGB, Mid Tower, Vidro Temperado, ATX, Preto',
                'price' => 1299.00
            ],
            [
                'name' => 'Fonte Corsair HX1500i 1500W 80 Plus Platinum',
                'description' => 'Fonte Corsair HX1500i 1500W, 80 Plus Platinum, Full Modular, PFC Ativo, Preta',
                'price' => 3299.00
            ],
            [
                'name' => 'Monitor LG UltraGear 32" 4K 144Hz OLED',
                'description' => 'Monitor Gamer LG UltraGear 32GS95UE, 32", 4K UHD, OLED, 144Hz, 0.03ms, DisplayHDR 400, G-Sync/FreeSync',
                'price' => 8999.00
            ],
            [
                'name' => 'Monitor Samsung Odyssey Neo G9 57" Dual 4K',
                'description' => 'Monitor Gamer Samsung Odyssey Neo G9, 57", Dual UHD, Mini LED, 240Hz, 1ms, HDR1000, Curvo',
                'price' => 15999.00
            ],
            [
                'name' => 'Teclado Mecânico Logitech G Pro X TKL',
                'description' => 'Teclado Mecânico Logitech G Pro X TKL, Switch GX Blue, RGB LIGHTSYNC, Wireless, Preto',
                'price' => 899.00
            ],
            [
                'name' => 'Mouse Logitech G Pro X Superlight 2',
                'description' => 'Mouse Gamer Logitech G Pro X Superlight 2, Wireless, 32000 DPI, Hero 2, 60g, Preto',
                'price' => 899.00
            ],
            [
                'name' => 'Headset SteelSeries Arctis Nova Pro Wireless',
                'description' => 'Headset Gamer SteelSeries Arctis Nova Pro Wireless, Multi-plataforma, Base de Carregamento, Hi-Res Audio',
                'price' => 2499.00
            ],
            [
                'name' => 'Mousepad Razer Strider XXL',
                'description' => 'Mousepad Gamer Razer Strider XXL, Híbrido, 940x410x3mm, Base Antiderrapante, Preto',
                'price' => 299.00
            ],
            [
                'name' => 'Cadeira Gamer DXRacer Master Series',
                'description' => 'Cadeira Gamer DXRacer Master Series, Ergonômica, Couro PU Premium, Reclinável 135°, Preta',
                'price' => 3499.00
            ],
            [
                'name' => 'Webcam Logitech StreamCam Plus 4K',
                'description' => 'Webcam Logitech StreamCam Plus, 4K 60fps, Auto-foco, HDR, USB-C, Preta',
                'price' => 1299.00
            ],
            [
                'name' => 'Microfone HyperX QuadCast S RGB',
                'description' => 'Microfone HyperX QuadCast S, USB, Condensador, Anti-Vibração, Pop Filter, RGB',
                'price' => 899.00
            ],
            [
                'name' => 'Placa de Captura Elgato 4K60 Pro MK.2',
                'description' => 'Placa de Captura Elgato Game Capture 4K60 Pro MK.2, PCIe, 4K 60fps, HDR10, Ultra Low Latency',
                'price' => 1799.00
            ],
            [
                'name' => 'Kit Cooler Lian Li UNI FAN SL V2 120mm',
                'description' => 'Kit 3x Cooler Lian Li UNI FAN SL V2 120mm, RGB, PWM, Conexão Magnética, Controladora Incluída',
                'price' => 699.00
            ],
            [
                'name' => 'SSD WD Black SN850X 2TB NVMe Gen4',
                'description' => 'SSD WD Black SN850X 2TB, M.2 NVMe, PCIe Gen4, Leitura 7300MB/s, Para Games',
                'price' => 1199.00
            ],
        ]);
    }
}
