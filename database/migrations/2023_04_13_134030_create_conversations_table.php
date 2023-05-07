<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id_1');
            $table->foreign('user_id_1')->references('id')->on('users');
            $table->unsignedBigInteger('user_id_2');
            $table->foreign('user_id_2')->references('id')->on('users');
            $table->dateTime('conversation_start_time');
            $table->dateTime('conversation_end_time')->nullable();
            $table->text('message_content');
            $table->enum('message_type', ['text', 'image', 'audio']);
            $table->unsignedBigInteger('message_id')->nullable();
            $table->enum('message_status', ['sent', 'delivered', 'read']);
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->enum('conversation_status', ['active', 'finished', 'cancelled']);
            $table->timestamps();
        });        
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};