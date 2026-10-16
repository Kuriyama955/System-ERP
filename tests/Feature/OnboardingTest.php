<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('facilities');
    Schema::dropIfExists('personal');
    Schema::dropIfExists('onboarding_task_karyawan');
    Schema::dropIfExists('onboarding_tasks');
    Schema::dropIfExists('karyawanstaffs');
    Schema::dropIfExists('departemens');

    Schema::create('karyawanstaffs', function ($table) {
        $table->id();
        $table->string('nama');
        $table->string('npk')->nullable();
        $table->string('departement')->nullable();
        $table->string('jabatan')->nullable();
        $table->string('lokasi')->nullable();
        $table->date('tmk')->nullable();
        $table->string('status')->default('ONBOARDING');
        $table->timestamps();
    });

    Schema::create('departemens', function ($table) {
        $table->id();
        $table->string('nama_departemen')->unique();
        $table->timestamps();
    });

    Schema::create('personal', function ($table) {
        $table->id();
        $table->foreignId('karyawanstaff_id')->nullable()->unique();
        $table->string('tempat_lahir')->nullable();
        $table->date('tanggal_lahir')->nullable();
        $table->text('alamat')->nullable();
        $table->string('no_telepon')->nullable();
        $table->string('email_pribadi')->nullable();
        $table->string('agama')->nullable();
        $table->string('status_pernikahan')->nullable();
        $table->string('pendidikan')->nullable();
        $table->string('pengalaman_kerja')->nullable();
        $table->string('informasi_tambahan')->nullable();
        $table->string('nama_bank')->nullable();
        $table->string('nomor_rekening')->nullable();
        $table->string('nama_pemilik_rekening')->nullable();
        $table->string('cabang_bank')->nullable();
        $table->string('nik')->nullable();
        $table->string('jenis_kelamin')->nullable();
        $table->string('golongan_darah')->nullable();
        $table->text('alamat_ktp')->nullable();
        $table->string('nama_kontak_darurat')->nullable();
        $table->string('hubungan_kontak_darurat')->nullable();
        $table->string('telepon_kontak_darurat')->nullable();
        $table->timestamps();
    });

    Schema::create('facilities', function ($table) {
        $table->id();
        $table->foreignId('karyawanstaff_id')->nullable();
        $table->string('nama_fasilitas');
        $table->string('kategori')->nullable();
        $table->string('nomor_aset')->nullable();
        $table->date('tanggal_pemberian')->nullable();
        $table->string('kondisi')->nullable();
        $table->string('status')->nullable();
        $table->date('tanggal_pengembalian')->nullable();
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });

    $migration = require base_path('database/migrations/2026_10_14_000000_create_onboarding_tables.php');
    $migration->up();
});

afterEach(function () {
    $migration = require base_path('database/migrations/2026_10_14_000000_create_onboarding_tables.php');
    $migration->down();
    Schema::dropIfExists('facilities');
    Schema::dropIfExists('personal');
    Schema::dropIfExists('karyawanstaffs');
    Schema::dropIfExists('departemens');
});

test('HRD can manage master tasks, assign tasks to multiple employees and update individual statuses', function () {
    $firstStaffId = DB::table('karyawanstaffs')->insertGetId([
        'nama' => 'Ayu',
        'npk' => '1001',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $secondStaffId = DB::table('karyawanstaffs')->insertGetId([
        'nama' => 'Budi',
        'npk' => '1002',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('onboarding.index'))
        ->assertOk()
        ->assertSee('Daftar Task Onboarding')
        ->assertDontSee('Status Task Karyawan');

    $this->post(route('onboarding.tasks.store'), ['nama_task' => 'Orientasi perusahaan'])
        ->assertRedirect(route('onboarding.index'));

    $taskId = DB::table('onboarding_tasks')->value('id');

    $this->post(route('onboarding.assignments.store'), [
        'onboarding_task_id' => $taskId,
        'karyawanstaff_ids' => [$firstStaffId, $secondStaffId],
    ])->assertRedirect(route('onboarding.tasks.show', $taskId));

    expect(DB::table('onboarding_task_karyawan')->count())->toBe(2)
        ->and(DB::table('onboarding_task_karyawan')->where('status', 'belum selesai')->count())->toBe(2);

    $firstAssignmentId = DB::table('onboarding_task_karyawan')
        ->where('karyawanstaff_id', $firstStaffId)
        ->value('id');

    $this->patch(route('onboarding.assignments.status', $firstAssignmentId), [
        'status' => 'selesai',
    ])->assertRedirect();

    $this->get(route('onboarding.tasks.show', $taskId))
        ->assertOk()
        ->assertSee('Ayu')
        ->assertSee('Budi')
        ->assertSee('Status Task')
        ->assertSee('Selesai')
        ->assertDontSee('Tandai Selesai');

    $this->post(route('onboarding.assignments.store'), [
        'onboarding_task_id' => $taskId,
        'karyawanstaff_ids' => [$firstStaffId],
    ])->assertRedirect(route('onboarding.tasks.show', $taskId));

    expect(DB::table('onboarding_task_karyawan')->count())->toBe(2)
        ->and(DB::table('onboarding_task_karyawan')
            ->where('karyawanstaff_id', $firstStaffId)
            ->value('status'))->toBe('selesai');

    $this->get(route('staff.show', $firstStaffId))
        ->assertOk()
        ->assertSee('Orientasi perusahaan')
        ->assertSee('1 dari 1 task selesai')
        ->assertSee('Selesai');
});

test('inactive master tasks cannot be assigned', function () {
    DB::table('karyawanstaffs')->insert([
        'nama' => 'Citra',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $staffId = DB::table('karyawanstaffs')->value('id');
    $taskId = DB::table('onboarding_tasks')->insertGetId([
        'nama_task' => 'Siapkan akses kerja',
        'is_active' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->from(route('onboarding.index'))
        ->post(route('onboarding.assignments.store'), [
            'onboarding_task_id' => $taskId,
            'karyawanstaff_ids' => [$staffId],
        ])
        ->assertSessionHasErrors('onboarding_task_id');

    expect(DB::table('onboarding_task_karyawan')->count())->toBe(0);
});

test('HRD can add an active onboarding task from an employee profile', function () {
    $staffId = DB::table('karyawanstaffs')->insertGetId([
        'nama' => 'Dewi',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $taskId = DB::table('onboarding_tasks')->insertGetId([
        'nama_task' => 'Pengenalan area kerja',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('staff.show', $staffId))
        ->assertOk()
        ->assertSee('Tambah Task')
        ->assertSee('Pengenalan area kerja');

    $this->post(route('onboarding.staff.assign', $staffId), [
        'onboarding_task_id' => $taskId,
    ])->assertRedirect(route('staff.show', $staffId));

    expect(DB::table('onboarding_task_karyawan')
        ->where('karyawanstaff_id', $staffId)
        ->where('onboarding_task_id', $taskId)
        ->value('status'))->toBe('belum selesai');

    $this->get(route('staff.show', $staffId))
        ->assertDontSee('Pilih task onboarding');

    $this->from(route('staff.show', $staffId))
        ->post(route('onboarding.staff.assign', $staffId), [
            'onboarding_task_id' => $taskId,
        ])
        ->assertSessionHasErrors('onboarding_task_id');

    expect(DB::table('onboarding_task_karyawan')->count())->toBe(1);
});

test('new staff start in onboarding and HRD can update their employment status', function () {
    DB::table('departemens')->insert([
        'nama_departemen' => 'Produksi',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->post(route('staff.store'), [
        'nama' => 'Rina',
        'npk' => '2001',
        'departement' => 'Produksi',
        'jabatan' => 'Operator',
        'lokasi' => 'Pabrik',
        'tmk' => '2026-10-15',
    ])->assertRedirect();

    $staffId = DB::table('karyawanstaffs')->where('npk', '2001')->value('id');
    expect(DB::table('karyawanstaffs')->where('id', $staffId)->value('status'))
        ->toBe('ONBOARDING');

    $this->get(route('staff.index', ['status' => 'ONBOARDING']))
        ->assertOk()
        ->assertSee('Rina')
        ->assertSee('Onboarding');

    $employeeData = [
        'nama' => 'Rina',
        'npk' => '2001',
        'departement' => 'Produksi',
        'jabatan' => 'Operator',
        'lokasi' => 'Pabrik',
        'tmk' => '2026-10-15',
    ];

    $this->put(route('staff.update', $staffId), $employeeData + ['status' => 'AKTIF'])
        ->assertRedirect(route('staff.show', $staffId));
    expect(DB::table('karyawanstaffs')->where('id', $staffId)->value('status'))
        ->toBe('AKTIF');
    $this->get(route('staff.show', $staffId))
        ->assertOk()
        ->assertSee('Karyawan Aktif');

    $this->put(route('staff.update', $staffId), $employeeData + ['status' => 'TIDAK AKTIF'])
        ->assertRedirect(route('staff.show', $staffId));
    expect(DB::table('karyawanstaffs')->where('id', $staffId)->value('status'))
        ->toBe('TIDAK AKTIF');
});

test('new staff are automatically assigned all active onboarding tasks', function () {
    DB::table('departemens')->insert([
        'nama_departemen' => 'Produksi',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $activeTaskIds = collect(['Lengkapi data', 'Induksi'])->map(fn ($name) => DB::table('onboarding_tasks')->insertGetId([
        'nama_task' => $name,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]));
    DB::table('onboarding_tasks')->insert([
        'nama_task' => 'Task nonaktif',
        'is_active' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->post(route('staff.store'), [
        'nama' => 'Nina',
        'npk' => '3001',
        'departement' => 'Produksi',
        'jabatan' => 'Operator',
        'lokasi' => 'Pabrik',
        'tmk' => '2026-10-15',
    ])->assertRedirect();

    $staffId = DB::table('karyawanstaffs')->where('npk', '3001')->value('id');
    $assignments = DB::table('onboarding_task_karyawan')
        ->where('karyawanstaff_id', $staffId)
        ->orderBy('onboarding_task_id')
        ->get();

    expect($assignments)->toHaveCount(2)
        ->and($assignments->pluck('onboarding_task_id')->all())->toBe($activeTaskIds->sort()->values()->all())
        ->and($assignments->pluck('status')->unique()->all())->toBe(['belum selesai']);

    $this->get(route('staff.show', $staffId))
        ->assertOk()
        ->assertSee('Lengkapi data')
        ->assertSee('Induksi')
        ->assertDontSee('Task nonaktif');
});
