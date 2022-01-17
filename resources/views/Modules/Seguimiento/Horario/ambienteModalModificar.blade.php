<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
    <div class="col-lg-6 col-md-6">
      <label>Descripci&oacute;n:</label>
      <input value="{{ $ambientes[$ambienteId]['pla_amb_descripcion'] }}" class="form-control" type="text" name="pla_amb_descripcion">
    </div>	
    <div class="col-lg-6 col-md-6">
      <label>Tipo:</label>
      <select required class="form-control" name="pla_amb_tipo">
        <option value="">Seleccione</option>
        @if($ambientes[$ambienteId]['pla_amb_tipo'] == 'Interno')
          <option selected value="Interno">Interno</option>
        @else
          <option value="Interno">Interno</option>
        @endif

        @if($ambientes[$ambienteId]['pla_amb_tipo'] == 'Externo')
          <option selected value="Externo">Externo</option>
        @else
          <option value="Externo">Externo</option>
        @endif

        @if($ambientes[$ambienteId]['pla_amb_tipo'] == 'Restriccion')
          <option selected value="Restriccion">Restricci&oacute;n</option>
        @else
        <option value="Restriccion">Restricci&oacute;n</option>
        @endif
      </select>
    </div>
    <div class="col-lg-6 col-md-6">
      <label>Suma horas al instructor?</label>
      <select required class="form-control" name="pla_amb_suma_horas">
        <option value="">Seleccione</option>
        @if($ambientes[$ambienteId]['pla_amb_suma_horas'] == 'SI')
          <option selected value="SI">SI</option>
          <option value="NO">NO</option>
        @endif

        @if($ambientes[$ambienteId]['pla_amb_suma_horas'] == 'NO')
          <option selected value="NO">NO</option>
          <option value="SI">SI</option>
        @endif
      </select>
    </div>
    <div class="col-lg-6 col-md-6">
      <label>Estado</label>
      <select required class="form-control" name="pla_amb_estado">
        <option value="">Seleccione</option>
        @if($ambientes[$ambienteId]['pla_amb_estado'] == 'Activo')
          <option selected value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
        @endif

        @if($ambientes[$ambienteId]['pla_amb_estado'] == 'Inactivo')
          <option selected value="Inactivo">Inactivo</option>
          <option value="Activo">Activo</option>
        @endif
      </select>
    </div>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
    <div class="col-lg-4 col-lg-push-4 col-md-4 col-md-push-4">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="pla_amb_id" value="{{ $ambienteId }}">
        <button class="btn btn-success btn-xs form-control" style="margin:15px 0px 0px 5px">Guardar cambios</button>
    </div>
  </div>
  <div id="notificaciones" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding-top: 15px; display:none;">
    <div class="col-lg-10 col-lg-push-1 col-md-10 col-md-push-1 col-sm-10 col-md-push-1 col-xs-12 text-center" style="background-color:#f1f1f1; border: 1px solid; padding:5px; border-radius: 8px;">
        <h4 id="mensaje"></h4>
    </div>
  </div>
</div>