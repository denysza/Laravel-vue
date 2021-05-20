<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examples', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->comment('ID');
            $table->integer('painter_id')->unsigned()->comment('業者ID');
            $table->integer('contract_id')->unsigned()->comment('契約ID');
            $table->string('image_file1', 256)->nullable()->default(null)->comment('画像１');
            $table->string('image_file2', 256)->nullable()->default(null)->comment('画像２');
            $table->string('image_file3', 256)->nullable()->default(null)->comment('画像３');
            $table->string('image_file4', 256)->nullable()->default(null)->comment('画像４');
            $table->string('image_file5', 256)->nullable()->default(null)->comment('画像５');
            $table->string('image_file6', 256)->nullable()->default(null)->comment('画像６');
            $table->text('comment')->nullable()->default(null)->comment('コメント');
            $table->tinyInteger('public_consent')->unsigned()->nullable()->default(null)->comment('公開承諾フラグ');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');
            $table->dateTime('created_at')->nullable()->default(null)->comment('作成日時');
            $table->dateTime('updated_at')->nullable()->default(null)->comment('更新日時');
        });

        DB::statement("ALTER TABLE examples COMMENT '施工事例'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examples');
    }
}
