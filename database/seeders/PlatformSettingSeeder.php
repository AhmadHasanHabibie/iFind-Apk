<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $userQrisSource = database_path('seeders/assets/qris-admin.jpg');
        $qrisPath = 'qris/admin/qris-admin.jpg';

        if (file_exists($userQrisSource)) {
            Storage::disk('public')->put($qrisPath, file_get_contents($userQrisSource));
        } elseif (! Storage::disk('public')->exists($qrisPath)) {
            $img = imagecreatetruecolor(500, 500);
            $bg = imagecolorallocate($img, 255, 255, 255);
            $navy = imagecolorallocate($img, 15, 23, 42);
            $teal = imagecolorallocate($img, 13, 148, 136);

            imagefill($img, 0, 0, $bg);

            // Border
            imagesetthickness($img, 6);
            imagerectangle($img, 15, 15, 485, 485, $navy);

            // Header text
            imagestring($img, 5, 140, 40, 'QRIS RESMI ADMIN iFIND', $teal);
            imagestring($img, 4, 110, 70, 'NMID: ID10202619000188', $navy);

            // Dummy QR Box
            imagesetthickness($img, 3);
            imagerectangle($img, 90, 110, 410, 410, $navy);

            // Corner marker boxes like a real QR code
            imagefilledrectangle($img, 110, 130, 170, 190, $navy);
            imagefilledrectangle($img, 125, 145, 155, 175, $bg);

            imagefilledrectangle($img, 330, 130, 390, 190, $navy);
            imagefilledrectangle($img, 345, 145, 375, 175, $bg);

            imagefilledrectangle($img, 110, 330, 170, 390, $navy);
            imagefilledrectangle($img, 125, 345, 155, 375, $bg);

            // Center dummy pattern
            imagestring($img, 5, 170, 250, '[ SCAN UNTUK BAYAR ]', $teal);
            imagestring($img, 4, 180, 275, 'PAJAK PLATFORM 5%', $navy);

            imagestring($img, 3, 115, 435, 'Dicetak oleh Bank Indonesia / ASPI', $navy);
            imagestring($img, 3, 160, 455, 'Aplikasi iFind Indonesia', $teal);

            ob_start();
            imagepng($img);
            $content = ob_get_clean();
            imagedestroy($img);

            Storage::disk('public')->put($qrisPath, $content);
        }

        PlatformSetting::set('admin_qris_path', $qrisPath);
        PlatformSetting::set('admin_bank_name', 'Bank Central Asia (BCA)');
        PlatformSetting::set('admin_bank_account_number', '8735029188');
        PlatformSetting::set('admin_bank_account_holder', 'PT iFind Layanan Indonesia');
        PlatformSetting::set('service_fee_percentage', '5');
    }
}
