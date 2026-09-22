@extends('errors.layout')

@section('code', View::hasSection('code') ? View::yieldContent('code') : ($exception ? $exception->getStatusCode() : '500'))
@section('title', View::hasSection('title') ? View::yieldContent('title') : 'Terjadi Kesalahan')
@section('message', View::hasSection('message') ? View::yieldContent('message') : ($exception && $exception->getMessage() ? $exception->getMessage() : 'Terjadi kendala saat memproses permintaan Anda.'))
