<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTranskipToTbAudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_audios', function (Blueprint $table) {
            $table->json('detail')->nullable();
            $table->longText('full_text')->nullable();
            $table->json('most_occur')->nullable();
            $table->longText('url_wordcloud')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_audios', function (Blueprint $table) {
            //
        });
    }
}
