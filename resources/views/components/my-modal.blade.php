<div>
     <dialog id="dialog">
        <button autofocus>Close</button>
        <div>
           {{ $body }}
        </div>
        <div>
            {{ $footer }}
        </div>
      </dialog>
     <button class="btn btn-success" >opn modal</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dialog = document.querySelector("dialog");
        const showButton = document.querySelector("dialog + button");
        const closeButton = document.querySelector("dialog button");

        // "Show the dialog" button opens the dialog modally
        showButton.addEventListener("click", () => {
        dialog.showModal();
        });

        // "Close" button closes the dialog
        closeButton.addEventListener("click", () => {
        dialog.close();
        });
    });
</script>

<style scoped>
    * {
      box-sizing: border-box;
    }

    .modal-mask {
      position: fixed;
      z-index: 9998;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      transition: opacity 0.3s ease;
    }

    .modal-container {
      width: 700px;
      margin: 40px auto 0;
      background-color: #fff;
      color: #1d70b8;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
      transition: all 0.3s ease;
      font-family: Helvetica, Arial, sans-serif;
    }

    .modal-header {
      background: #1d70b8;
      border-radius: 0;
    }

    .modal-footer {
      background: #1d70b8;
      border-radius: 0;
    }

    .modal-header h3 {
      margin-top: 0;
      color: #fff;
    }

    .modal-body {
      margin: 0;
    }

    div .form-group {
      margin: 0;
    }

    .text-right {
      text-align: right;
    }

    .form-label {
      display: block;
      margin-bottom: 1em;
      color: black;
    }

    .btn-close {
      float: right;
      font-weight: 1000;
    }

    .form-label > .form-control {
      margin-top: 0.5em;
    }

    .form-control {
      display: block;
      width: 100%;
      padding: 0.5em 1em;
      line-height: 1.5;
      border: 1px solid #ddd;
    }

    /*
     * The following styles are auto-applied to elements with
     * transition="modal" when their visibility is toggled
     * by Vue.js.
     *
     * You can easily play with the modal transition by editing
     * these styles.
     */

    .modal-enter {
      opacity: 0;
    }

    .modal-leave-active {
      opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
      -webkit-transform: scale(1.1);
      transform: scale(1.1);
    }
</style>
