<template>
  <div>
  	<div class="form-group row">
  		<div class="form-group col-md-3">
  			<label for="nome">Nome</label>
        		<input v-model.trim="form.name"  @input="$v.form.name.$touch()" type="text" class="form-control" v-bind:class="{ 'is-invalid': $v.form.name.$invalid && $v.form.name.$dirty}" placeholder="Nome Veicolo..." name="nome" >
            <div class="invalid-feedback"  v-if="!$v.form.name.required">Il nome del veicolo è obbligatorio</div>
      </div>
  		<div class="form-group col-md-3">
  			<label for="targa">Targa</label>
        <input v-model.trim="form.targa" type="text" class="form-control" placeholder="Targa Veicolo" name="targa" @input="$v.form.targa.$touch()"v-bind:class="{ 'is-invalid': $v.form.targa.$invalid && $v.form.targa.$dirty}"  >
        <div class="invalid-feedback"  v-if="!$v.form.targa.required">La targa del veicolo è obbligatorio</div>
      </div>
      <div class="form-goup col-md-3">
        <label for="marca">Marca</label>
          <select  v-model.trim="form.marca"  class="form-control" id="marca" name="marca">
            <option hidden disabled selected value="">Scegli...</option>
            <slot name="marche"></slot>
          </select>
      </div>
      <div class="form-goup col-md-3">
        <label for="alimentazione">Modello</label>
          <input v-model.trim="form.modello"  @input="$v.form.modello.$touch()" type="text" class="form-control" v-bind:class="{ 'is-invalid': $v.form.modello.$invalid && $v.form.modello.$dirty}" placeholder="Es. Doblò, Marea..." name="modello">
          <div class="invalid-feedback"  v-if="!$v.form.modello.required">Il modello del veicolo è obbligatorio</div>
      </div>
    	</div>
    	<div class="form-group row">
    		<div class="col-md-3">
    			<label for="impiego">Impiego</label>
            <select  v-model.trim="form.impiego"  class="form-control" id="impiego" name="impiego">
              <option hidden disabled selected value="">Scegli...</option>
              <slot name="impieghi"></slot>
            </select>
        </div>
    		<div class="form-goup col-md-3">
    			<label for="tipologia">Tipologia</label>
          <select  v-model.trim="form.tipologia"  class="form-control" id="tipologia" name="tipologia">
              <option hidden disabled selected value="">Scegli...</option>
              <slot name="tipologie"></slot>
          </select>
    		</div>
    		<div class="form-goup col-md-3">
          <label for="alimentazione">Alimentazione</label>
        		<select  v-model.trim="form.alimentazione"  class="form-control" id="alimentazione" name="alimentazione">
              <option hidden disabled selected value="">Scegli...</option>
        			<slot name="alimentazioni"></slot>
        		</select>
    		</div>
    		<div class="form-goup col-md-3">
    			<label for="posti">Numero posti</label>
        	<input type="number" v-model.trim="form.posti" class="form-control" v-bind:class="{ 'is-invalid': $v.form.posti.$invalid &&  $v.form.posti.$dirty}" @input="$v.form.posti.$touch()"  name="posti">
          <div class="invalid-feedback"  v-if="!$v.form.posti.required">I posti sono obbligatori</div>
          <div class="invalid-feedback" v-if="!$v.form.posti.minvalue">I posti devono essere un numero positivo</div>
       </div>
    	</div>
        <button class="btn btn-primary"  align='right' v-bind:disabled="$v.form.$invalid" type="submit">Salva</button>
        <button class="btn btn-primary" name="_addanother" v-bind:disabled="$v.form.$invalid"  value="true" type="submit">Salva e aggiungi un'altro veicolo</button>

  </div>
</template>

<script>
  import { required, minLength, minValue, between } from 'vuelidate/lib/validators'

  export default {
    data () {
      return {
        form: {
          name: '',
          posti: '',
          targa: '',
          modello: '',
          marca: '',
          alimentazione: '',
          impiego: '',
          tipologia: '',
        }
      }
    },
    validations: {
      form:{
        name: {
          required,
        },
        targa: {
          required,
        },
        posti: {
          required,
          minValue: minValue(1)
        },
        modello : {
          required,
        },
        marca : {
          required,
        },
        alimentazione : {
          required,
        },
        impiego : {
          required
        },
        tipologia : {
          required
        }
      }
    }
  }
</script>
