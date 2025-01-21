@if (Session::has("success"))
    <div class="alert alert-success" role="alert">
        <strong>{{ Session::get("success") }}</strong>
        <a
            href="#"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="close"
        >
            &times;
        </a>
    </div>
@elseif (Session::has("warning"))
    <div class="alert alert-warning" role="alert">
        <strong>{{ Session::get("warning") }}</strong>
        <a
            href="#"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="close"
        >
            &times;
        </a>
    </div>
@elseif (Session::has("error"))
    <div class="alert alert-danger" role="alert">
        <strong>{{ Session::get("error") }}</strong>
        <a
            href="#"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="close"
        >
            &times;
        </a>
    </div>
@endif
