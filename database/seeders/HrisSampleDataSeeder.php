<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Karyawanstaff;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Seeder;
use RuntimeException;

class HrisSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('HrisSampleDataSeeder hanya boleh dijalankan di environment non-production.');
        }

        $karyawans = Karyawan::query()->orderBy('id')->limit(3)->get();
        $karyawanstaffs = Karyawanstaff::query()->orderBy('id')->limit(3)->get();

        if ($karyawans->count() < 3 || $karyawanstaffs->count() < 3) {
            throw new RuntimeException(
                'Seeder membutuhkan minimal 3 record pada tabel karyawans dan 3 record pada tabel karyawanstaffs.'
            );
        }

        $faker = Factory::create('id_ID');
        $faker->seed(20261009);

        foreach ($karyawans as $karyawan) {
            $this->seedRelations(
                $karyawan->personal(),
                $karyawan->facilities(),
                $karyawan->id,
                'karyawan',
                $karyawan->nama,
                $faker
            );
        }

        foreach ($karyawanstaffs as $karyawanstaff) {
            $this->seedRelations(
                $karyawanstaff->personal(),
                $karyawanstaff->facilities(),
                $karyawanstaff->id,
                'karyawanstaff',
                $karyawanstaff->nama,
                $faker
            );
        }
    }

    private function seedRelations(
        HasOne $personalRelation,
        HasMany $facilitiesRelation,
        int $employeeId,
        string $employeeType,
        ?string $employeeName,
        Generator $faker
    ): void {
        $personalRelation->firstOrCreate([], [
            'nik' => 'DEMO-NIK-'.$employeeType.'-'.$employeeId,
            'tempat_lahir' => $faker->city(),
            'tanggal_lahir' => $faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
            'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
            'status_pernikahan' => $faker->randomElement(['Belum Menikah', 'Menikah']),
            'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'golongan_darah' => $faker->randomElement(['A', 'B', 'AB', 'O']),
            'alamat' => $faker->address(),
            'no_telepon' => $faker->numerify('08##########'),
            'email_pribadi' => $faker->unique()->safeEmail(),
            'pendidikan' => $faker->randomElement(['SMA/SMK', 'D3', 'S1']),
            'pengalaman_kerja' => $faker->randomElement([
                'Belum ada pengalaman kerja',
                'Pengalaman kerja 1-3 tahun',
                'Pengalaman kerja lebih dari 3 tahun',
            ]),
            'informasi_tambahan' => 'DATA DEMO - bukan data personal sebenarnya',
            'nama_bank' => $faker->randomElement(['Bank Mandiri', 'BCA', 'BNI', 'BRI']),
            'nomor_rekening' => 'DEMO-REK-'.$employeeId,
            'nama_pemilik_rekening' => $employeeName ?: 'DATA DEMO',
            'cabang_bank' => $faker->city(),
            'nama_kontak_darurat' => $faker->name(),
            'hubungan_kontak_darurat' => $faker->randomElement(['Keluarga', 'Saudara', 'Pasangan']),
            'telepon_kontak_darurat' => $faker->numerify('08##########'),
        ]);

        if (! $facilitiesRelation->exists()) {
            $facilitiesRelation->create([
                'nama_fasilitas' => 'Laptop kerja',
                'kategori' => 'Elektronik',
                'nomor_aset' => 'DEMO-AST-'.$employeeType.'-'.$employeeId,
                'tanggal_pemberian' => now()->toDateString(),
                'kondisi' => 'Baik',
                'status' => 'Diberikan',
                'keterangan' => 'DATA DEMO - bukan riwayat aset sebenarnya',
            ]);
        }
    }
}
