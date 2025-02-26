@extends("layout.main")

@section("title", "Manutenzione")

@section("content")
    @include("partials.header", ["title" => "Manutenzioni Programmate"])
    <div class="container">
        <my-table
            :header="[{'label':'#', 'width':'10%'}, {'label':'Nome', 'width':'50%'}, {'label':'Ore', 'width':'20%'}]"
            data-url="{{ route("api.manutenzioni.programmate") }}"
            url="{{ route("manutenzioni.programmate.save") }}"
        ></my-table>
    </div>
@endsection
