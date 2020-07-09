<?php

namespace Tests\Unit;

use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class MonitorTest extends TestCase
{
    use RefreshDatabase;
    // Don't use "RefreshDatabase" in production
    /**
     * The data sent is wrong.
     *"level" => "no.existe" does not exists.
     *
     * @test
     */
    public function wrong_data_is_sent()
    {
        $response = $this->post('/api/logger/log', [
            "title" => "Titulo",
            "extra" => [
                "email" => "test@asd.com",
                "password" => "*****"
            ],
            "config" => [
                "db" => 1,
                "file" => 1,
                "level" => "debug",
            ]
        ]);

        $this->assertCount(0, Log::all());
    }

    /**
     * Ensures that the event received is saved in the database.
     *
     * @test
     */
    public function saves_json_in_db()
    {
        $response = $this->post('/api/logger/log', [
            "title" => "Titulo",
            "from" => "Auth",
            "extra" => [
                "email" => "test@asd.com",
                "password" => "*****"
            ],
            "config" => [
                "db" => 1,
                "file" => 1,
                "level" => "debug",
            ]
        ]);
        $response->assertStatus(config('constants.CONFIG.HTTP_CREATED'));

        $this->assertCount(1, Log::all());
    }

    /**
     * Ensures that the event received is saved in the log.
     *
     * @test
     */
    public function saves_json_in_monolog()
    {
        $response = $this->post('/api/logger/log', [
            "title" => "Titulo",
            "from" => "Auth",
            "extra" => [
                "email" => "test@asd.com",
                "password" => "*****"
            ],
            "config" => [
                "db" => 1,
                "file" => 1,
                "level" => "debug",
            ]
        ]);

        $response->assertStatus(config('constants.CONFIG.HTTP_CREATED'));
    }

    /**
     * Ensures that the log is sent to the frontend.
     *
     * @test
     */

    public function get_info_about_logs()
    {
        factory(Log::class)->create();

        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        $date_log = $year . '-' . $month . '-' . $day;

        $response = $this->get('/api/logger/log?date=' . $date_log);

        $response->assertStatus(config('constants.CONFIG.HTTP_CODE_OK'));
        $response->assertJsonFragment([
            'title' => 'Titulo',
        ]);
    }

    /**
     * Ensures that the log is sent to the frontend.
     *
     * @test
     */

    public function file_log_does_not_exist()
    {
        $year = date('Y')+50;  //a year wont exist
        $month = date('m');
        $day = date('d');

        $date_log = $year . '-' . $month . '-' . $day;

        $response = $this->get('/api/logger/log?date=' . $date_log);

        $response->assertStatus(config('constants.CONFIG.HTTP_CODE_BAD_REQUEST'));
    }

     /**
     * Ensures that the event received is saved in the database.
     *
     * @test
     */
    public function saves_json_in_db_exception()
    {
        Schema::dropIfExists(config('constants.CONFIG.TABLE_PREFIX') .'logs_data');

        $response = $this->post('/api/logger/log', [
            "title" => "Titulo",
            "from" => "Auth",
            "extra" => [
                "email" => "test@asd.com",
                "password" => "*****"
            ],
            "config" => [
                "db" => 1,
                "file" => 1,
                "level" => "debug",
            ]
        ]);

        $response->assertStatus(config('constants.CONFIG.HTTP_CODE_INTERNAL_SERVER_ERROR'));
    }

   /**
     * Ensures that the log is sent to the frontend.
     *
     * @test
     */

    public function get_info_about_logs_exception()
    {
        Schema::dropIfExists(config('constants.CONFIG.TABLE_PREFIX') . 'logs_data');
        
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        $date_log = $year . '-' . $month . '-' . $day;

        $response = $this->get('/api/logger/log?date=' . $date_log);

        $response->assertStatus(config('constants.CONFIG.HTTP_CODE_INTERNAL_SERVER_ERROR'));
    }
}
