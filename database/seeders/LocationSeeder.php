<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::create(['name' => 'Nanggroe Aceh Darussalam', 'type' => 'provinsi', 'lat' => '5.5483', 'lng' => '95.3238', 'parent_id' => null, 'order' => 1]);
        Location::create(['name' => 'Sumatera Utara', 'type' => 'provinsi', 'lat' => '3.5952', 'lng' => '98.6722', 'parent_id' => null, 'order' => 2]);
        Location::create(['name' => 'Sumatera Barat', 'type' => 'provinsi', 'lat' => '-0.9471', 'lng' => '100.4172', 'parent_id' => null, 'order' => 3]);
        Location::create(['name' => 'Riau', 'type' => 'provinsi', 'lat' => '0.5071', 'lng' => '101.4478', 'parent_id' => null, 'order' => 4]);
        Location::create(['name' => 'Kepulauan Riau', 'type' => 'provinsi', 'lat' => '1.1301', 'lng' => '104.0305', 'parent_id' => null, 'order' => 5]);
        Location::create(['name' => 'Jambi', 'type' => 'provinsi', 'lat' => '-1.6101', 'lng' => '103.6131', 'parent_id' => null, 'order' => 6]);
        Location::create(['name' => 'Sumatera Selatan', 'type' => 'provinsi', 'lat' => '-2.9909', 'lng' => '104.7566', 'parent_id' => null, 'order' => 7]);
        Location::create(['name' => 'Bangka Belitung', 'type' => 'provinsi', 'lat' => '-2.7411', 'lng' => '106.4406', 'parent_id' => null, 'order' => 8]);
        Location::create(['name' => 'Bengkulu', 'type' => 'provinsi', 'lat' => '-3.8004', 'lng' => '102.2655', 'parent_id' => null, 'order' => 9]);
        Location::create(['name' => 'Lampung', 'type' => 'provinsi', 'lat' => '-5.4295', 'lng' => '105.2615', 'parent_id' => null, 'order' => 10]);
        Location::create(['name' => 'Banten', 'type' => 'provinsi', 'lat' => '-6.4058', 'lng' => '106.0640', 'parent_id' => null, 'order' => 11]);
        Location::create(['name' => 'DKI Jakarta', 'type' => 'provinsi', 'lat' => '-6.2088', 'lng' => '106.8456', 'parent_id' => null, 'order' => 12]);
        Location::create(['name' => 'Jawa Barat', 'type' => 'provinsi', 'lat' => '-6.9039', 'lng' => '107.6186', 'parent_id' => null, 'order' => 13]);
        Location::create(['name' => 'Jawa Tengah', 'type' => 'provinsi', 'lat' => '-7.0051', 'lng' => '110.4381', 'parent_id' => null, 'order' => 14]);
        Location::create(['name' => 'Daerah Istimewa Yogyakarta', 'type' => 'provinsi', 'lat' => '-7.7956', 'lng' => '110.3695', 'parent_id' => null, 'order' => 15]);
        Location::create(['name' => 'Jawa Timur', 'type' => 'provinsi', 'lat' => '-7.2504', 'lng' => '112.7688', 'parent_id' => null, 'order' => 16]);
        Location::create(['name' => 'Bali', 'type' => 'provinsi', 'lat' => '-8.3405', 'lng' => '115.0920', 'parent_id' => null, 'order' => 17]);
        Location::create(['name' => 'Nusa Tenggara Barat', 'type' => 'provinsi', 'lat' => '-8.6525', 'lng' => '116.3249', 'parent_id' => null, 'order' => 18]);
        Location::create(['name' => 'Nusa Tenggara Timur', 'type' => 'provinsi', 'lat' => '-10.1772', 'lng' => '123.6070', 'parent_id' => null, 'order' => 19]);
        Location::create(['name' => 'Kalimantan Barat', 'type' => 'provinsi', 'lat' => '-0.0263', 'lng' => '109.3425', 'parent_id' => null, 'order' => 20]);
        Location::create(['name' => 'Kalimantan Tengah', 'type' => 'provinsi', 'lat' => '-1.6815', 'lng' => '113.3824', 'parent_id' => null, 'order' => 21]);
        Location::create(['name' => 'Kalimantan Selatan', 'type' => 'provinsi', 'lat' => '-3.3194', 'lng' => '114.5908', 'parent_id' => null, 'order' => 22]);
        Location::create(['name' => 'Kalimantan Timur', 'type' => 'provinsi', 'lat' => '-0.5021', 'lng' => '117.1537', 'parent_id' => null, 'order' => 23]);
        Location::create(['name' => 'Kalimantan Utara', 'type' => 'provinsi', 'lat' => '3.3273', 'lng' => '117.5785', 'parent_id' => null, 'order' => 24]);
        Location::create(['name' => 'Sulawesi Utara', 'type' => 'provinsi', 'lat' => '1.4748', 'lng' => '124.8421', 'parent_id' => null, 'order' => 25]);
        Location::create(['name' => 'Gorontalo', 'type' => 'provinsi', 'lat' => '0.5436', 'lng' => '123.0568', 'parent_id' => null, 'order' => 26]);
        Location::create(['name' => 'Sulawesi Tengah', 'type' => 'provinsi', 'lat' => '-0.9000', 'lng' => '119.8777', 'parent_id' => null, 'order' => 27]);
        Location::create(['name' => 'Sulawesi Barat', 'type' => 'provinsi', 'lat' => '-2.6786', 'lng' => '118.8894', 'parent_id' => null, 'order' => 28]);
        Location::create(['name' => 'Sulawesi Selatan', 'type' => 'provinsi', 'lat' => '-5.1477', 'lng' => '119.4327', 'parent_id' => null, 'order' => 29]);
        Location::create(['name' => 'Sulawesi Tenggara', 'type' => 'provinsi', 'lat' => '-3.9676', 'lng' => '122.5946', 'parent_id' => null, 'order' => 30]);
        Location::create(['name' => 'Maluku', 'type' => 'provinsi', 'lat' => '-3.6950', 'lng' => '128.1814', 'parent_id' => null, 'order' => 31]);
        Location::create(['name' => 'Maluku Utara', 'type' => 'provinsi', 'lat' => '0.7893', 'lng' => '127.3967', 'parent_id' => null, 'order' => 32]);
        Location::create(['name' => 'Papua', 'type' => 'provinsi', 'lat' => '-2.5337', 'lng' => '140.7181', 'parent_id' => null, 'order' => 33]);
        Location::create(['name' => 'Papua Barat', 'type' => 'provinsi', 'lat' => '-0.8615', 'lng' => '134.0620', 'parent_id' => null, 'order' => 34]);
        Location::create(['name' => 'Papua Tengah', 'type' => 'provinsi', 'lat' => '-2.1363', 'lng' => '137.4945', 'parent_id' => null, 'order' => 35]);
        Location::create(['name' => 'Papua Pegunungan', 'type' => 'provinsi', 'lat' => '-4.0830', 'lng' => '138.9576', 'parent_id' => null, 'order' => 36]);
        Location::create(['name' => 'Papua Selatan', 'type' => 'provinsi', 'lat' => '-7.7750', 'lng' => '139.5743', 'parent_id' => null, 'order' => 37]);
        Location::create(['name' => 'Papua Barat Daya', 'type' => 'provinsi', 'lat' => '-1.3361', 'lng' => '131.0188', 'parent_id' => null, 'order' => 38]);
    }
}
