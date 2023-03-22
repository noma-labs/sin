@extends('errors::layout')

@section('title', 'SIN Error')

@section('message', __($exception->getMessage() ?: 'SIN error'))
