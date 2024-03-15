<!-- template for the modal component -->
<template id="modal-template">
  <span>
    <transition name="modal">
      <div class="modal-mask" @click="close" v-show="showModal">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h3>{{ modalTitle }}</h3>
            <button
              type="button"
              class="btn btn-danger btn-close"
              aria-label="Close"
              @click="close()"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <slot name="modal-body-slot">
              <!-- slot for modal body -->
            </slot>
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-danger" type="button" @click="close()">Chiudi</button> -->
            <slot name="modal-button"> <!-- slot additional button --> </slot>
          </div>
        </div>
      </div>
    </transition>
    <button
      v-bind:class="['btn', buttonStyle]"
      type="button"
      @click="showModal = true"
    >
      {{ buttonTitle }}
    </button>
  </span>
</template>

<script>
export default {
  props: {
    modalTitle: String,
    buttonTitle: String,
    buttonStyle: {
      type: String,
      default: "btn-primary",
    },
  },
  data() {
    return {
      showModal: false,
      // buttonStyle: 'btn-primary',
    };
  },
  methods: {
    close: function () {
      this.showModal = false;
    },
  },
};
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
