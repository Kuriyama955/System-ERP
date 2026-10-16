<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('training_participants');
    Schema::dropIfExists('trainings');
    Schema::dropIfExists('onboarding_task_karyawan');
    Schema::dropIfExists('onboarding_tasks');
    Schema::dropIfExists('assets');
    Schema::dropIfExists('facilities');
    Schema::dropIfExists('personal');
    Schema::dropIfExists('karyawanstaffs');

    Schema::create('karyawanstaffs', function ($table) {
        $table->id();
        $table->string('nama');
        $table->string('npk')->nullable();
        $table->string('departement')->nullable();
        $table->string('jabatan')->nullable();
        $table->string('lokasi')->nullable();
        $table->date('tmk')->nullable();
        $table->string('status')->default('AKTIF');
        $table->timestamps();
    });

    Schema::create('personal', function ($table) {
        $table->id();
        $table->unsignedBigInteger('karyawanstaff_id')->nullable();
        $table->timestamps();
    });

    Schema::create('facilities', function ($table) {
        $table->id();
        $table->unsignedBigInteger('karyawanstaff_id')->nullable();
        $table->unsignedBigInteger('asset_id')->nullable();
        $table->date('tanggal_pemberian')->nullable();
        $table->string('kondisi')->nullable();
        $table->date('tanggal_pengembalian')->nullable();
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });

    Schema::create('assets', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('code')->unique();
        $table->timestamps();
    });

    Schema::create('onboarding_tasks', function ($table) {
        $table->id();
        $table->string('nama_task');
        $table->timestamps();
    });

    Schema::create('onboarding_task_karyawan', function ($table) {
        $table->id();
        $table->unsignedBigInteger('onboarding_task_id');
        $table->unsignedBigInteger('karyawanstaff_id');
        $table->string('status')->default('belum selesai');
        $table->timestamps();
    });

    $trainingsMigration = require base_path('database/migrations/2026_10_09_083200_create_trainings_table.php');
    $trainingsMigration->up();
    $participantsMigration = require base_path('database/migrations/2026_10_17_000000_create_training_participants_table.php');
    $participantsMigration->up();
});

afterEach(function () {
    Schema::dropIfExists('training_participants');
    Schema::dropIfExists('trainings');
    Schema::dropIfExists('onboarding_task_karyawan');
    Schema::dropIfExists('onboarding_tasks');
    Schema::dropIfExists('facilities');
    Schema::dropIfExists('assets');
    Schema::dropIfExists('personal');
    Schema::dropIfExists('karyawanstaffs');
});

test('HRD can create a training with participants and see the participant list', function () {
    $firstStaffId = DB::table('karyawanstaffs')->insertGetId([
        'nama' => 'Ayu',
        'npk' => '1001',
        'departement' => 'HRD',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $secondStaffId = DB::table('karyawanstaffs')->insertGetId([
        'nama' => 'Budi',
        'npk' => '1002',
        'departement' => 'Produksi',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('training.index'))
        ->assertOk()
        ->assertSee('Pilih peserta yang mengikuti training ini')
        ->assertSee('Ayu')
        ->assertSee('Budi');

    $response = $this->post(route('training.store'), [
        '_form' => 'create',
        'nama_pelatihan' => 'Pelatihan K3',
        'tema' => 'Keselamatan Kerja',
        'tanggal_aktual' => '2026-10-17',
        'lokasi' => 'Ruang Training',
        'karyawanstaff_ids' => [$firstStaffId, $secondStaffId],
    ]);

    $trainingId = DB::table('trainings')->value('id');
    $response->assertRedirect(route('training.show', $trainingId));
    $this->assertDatabaseCount('training_participants', 2);
    $this->assertDatabaseHas('training_participants', [
        'training_id' => $trainingId,
        'karyawanstaff_id' => $firstStaffId,
    ]);

    $this->get(route('training.show', $trainingId))
        ->assertOk()
        ->assertSee('fonts.googleapis.com/css2?family=Material+Symbols+Outlined', false)
        ->assertSee('Ayu')
        ->assertSee('Budi')
        ->assertSee('Jumlah Peserta')
        ->assertSee('Rata-rata Nilai')
        ->assertDontSee('Status Isian')
        ->assertDontSee('Isian lengkap');

    DB::table('training_participants')
        ->where('training_id', $trainingId)
        ->where('karyawanstaff_id', $firstStaffId)
        ->update(['nilai_pre_test' => 80, 'nilai_post_test' => 90]);
    DB::table('training_participants')
        ->where('training_id', $trainingId)
        ->where('karyawanstaff_id', $secondStaffId)
        ->update(['nilai_pre_test' => 60]);

    $this->get(route('training.show', $trainingId))
        ->assertOk()
        ->assertSee('70')
        ->assertSee('90')
        ->assertSee('Nilai kosong tidak dihitung.');

    $this->get(route('training.index'))
        ->assertOk()
        ->assertSee('Total Peserta')
        ->assertSee('>2</td>', false)
        ->assertSee('href="'.route('training.show', $trainingId).'"', false)
        ->assertSee('title="Detail training"', false)
        ->assertSee('visibility</span>', false)
        ->assertSee('training-action-btn js-edit-training', false)
        ->assertSee('training-action-btn js-delete-training', false);
});

test('HRD can edit training participants without clearing existing participant data', function () {
    $staffIds = collect(['Ayu', 'Budi', 'Cici'])
        ->map(fn ($name) => DB::table('karyawanstaffs')->insertGetId([
            'nama' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    $trainingId = DB::table('trainings')->insertGetId([
        'nama_pelatihan' => 'Pelatihan K3',
        'tanggal_aktual' => '2026-10-17',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $existingParticipantId = DB::table('training_participants')->insertGetId([
        'training_id' => $trainingId,
        'karyawanstaff_id' => $staffIds[0],
        'nilai_pre_test' => 60,
        'nilai_post_test' => 90,
        'hasil_training' => 'Memahami materi.',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('training.index'))
        ->assertOk()
        ->assertSee('type="checkbox"', false)
        ->assertSee('data-participants="'.$staffIds[0].'"', false);

    $this->put(route('training.update', $trainingId), [
        '_form' => 'edit',
        'nama_pelatihan' => 'Pelatihan K3',
        'tema' => 'Keselamatan Kerja',
        'tanggal_aktual' => '2026-10-17',
        'lokasi' => 'Ruang Training',
        'karyawanstaff_ids' => [$staffIds[0], $staffIds[2]],
    ])->assertRedirect(route('training.index'));

    $this->assertDatabaseCount('training_participants', 2);
    $this->assertDatabaseHas('training_participants', [
        'id' => $existingParticipantId,
        'nilai_pre_test' => 60,
        'nilai_post_test' => 90,
        'hasil_training' => 'Memahami materi.',
    ]);
    $this->assertDatabaseHas('training_participants', [
        'training_id' => $trainingId,
        'karyawanstaff_id' => $staffIds[2],
    ]);
    $this->assertDatabaseMissing('training_participants', [
        'training_id' => $trainingId,
        'karyawanstaff_id' => $staffIds[1],
    ]);
});

test('HRD can update a staff training record from the staff profile', function () {
    $staffId = DB::table('karyawanstaffs')->insertGetId([
        'nama' => 'Ayu',
        'npk' => '1001',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $trainingId = DB::table('trainings')->insertGetId([
        'nama_pelatihan' => 'Pelatihan K3',
        'tanggal_aktual' => '2026-10-17',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $participantId = DB::table('training_participants')->insertGetId([
        'training_id' => $trainingId,
        'karyawanstaff_id' => $staffId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('staff.show', $staffId))
        ->assertOk()
        ->assertSee('Pelatihan K3')
        ->assertSee('id="trainingResultModal"', false)
        ->assertSee('Isi Hasil Training')
        ->assertSee('id="trainingResultParticipant"', false)
        ->assertSee('js-edit-training-result')
        ->assertSee('name="nilai_pre_test"', false);

    $this->put(route('staff.training-participants.update', [$staffId, $participantId]), [
        'nilai_pre_test' => 0,
        'nilai_post_test' => 85.5,
        'hasil_training' => 'Memahami prosedur keselamatan kerja.',
    ])->assertRedirect(route('staff.show', $staffId));

    $this->assertDatabaseHas('training_participants', [
        'id' => $participantId,
        'nilai_pre_test' => 0,
        'nilai_post_test' => 85.5,
        'hasil_training' => 'Memahami prosedur keselamatan kerja.',
    ]);

    $this->get(route('staff.show', $staffId))
        ->assertOk()
        ->assertSee('>0</td>', false)
        ->assertSee('>85.5</td>', false)
        ->assertSee('data-post-test="85.5"', false)
        ->assertSee('Memahami prosedur keselamatan kerja.');
});

test('training score formatting omits unnecessary trailing zeroes', function () {
    expect(\App\Models\TrainingParticipant::formatScore('90.00'))->toBe('90')
        ->and(\App\Models\TrainingParticipant::formatScore('85.50'))->toBe('85.5')
        ->and(\App\Models\TrainingParticipant::formatScore(null))->toBe('');
});

test('training scores must be between zero and one hundred', function () {
    $staffId = DB::table('karyawanstaffs')->insertGetId([
        'nama' => 'Ayu',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $trainingId = DB::table('trainings')->insertGetId([
        'nama_pelatihan' => 'Pelatihan K3',
        'tanggal_aktual' => '2026-10-17',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $participantId = DB::table('training_participants')->insertGetId([
        'training_id' => $trainingId,
        'karyawanstaff_id' => $staffId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->from(route('staff.show', $staffId))
        ->put(route('staff.training-participants.update', [$staffId, $participantId]), [
            'nilai_pre_test' => 101,
            'nilai_post_test' => 50,
            'hasil_training' => 'Selesai',
        ])
        ->assertSessionHasErrors('nilai_pre_test');

    $this->assertDatabaseHas('training_participants', ['id' => $participantId]);
    $this->assertDatabaseMissing('training_participants', [
        'id' => $participantId,
        'nilai_pre_test' => 101,
    ]);
});

test('demo training seeder creates trainings and sample participant records idempotently', function () {
    foreach (['Ayu', 'Budi'] as $index => $name) {
        DB::table('karyawanstaffs')->insert([
            'nama' => $name,
            'npk' => (string) (1001 + $index),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $seeder = app(Database\Seeders\HrisTrainingSeeder::class);
    $seeder->run();
    DB::table('training_participants')
        ->where('karyawanstaff_id', 1)
        ->update(['nilai_pre_test' => 90, 'nilai_post_test' => 90]);
    $seeder->run();

    $this->assertDatabaseCount('trainings', 3);
    $this->assertDatabaseCount('training_participants', 6);
    $this->assertDatabaseHas('training_participants', [
        'karyawanstaff_id' => 1,
        'nilai_pre_test' => 0,
        'nilai_post_test' => 0,
    ]);
    $this->assertDatabaseHas('training_participants', [
        'karyawanstaff_id' => 2,
        'nilai_pre_test' => null,
    ]);
});