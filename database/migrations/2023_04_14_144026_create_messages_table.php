<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->boolean('encrypted')->default(false);
            $table->unsignedBigInteger('sender_id');
            $table->string('message_type');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('conversation_id');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
