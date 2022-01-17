<div class="row" style="margin: 0px 0px 10px 0px;padding: 8px 0px 0px 0px;border: 1px solid;">
  <div class="col-lg-2 col-md-2">
    <label>D&iacute;a</label>
    <select required name="dia[1]" class="form-control dia">
      <option value="">Seleccione...</option>
      <?php foreach($dias as $val){
        echo '<option value="'.$val->pla_dia_id.'">'.$val->pla_dia_descripcion.'</option>';
      }   
      ?>
    </select> 
  </div>
  <div class="col-lg-2 col-md-2">
    <label>Hora inicio</label>
    <select required name="hora_inicio[1]" class="form-control hora_inicio">
      <option value="">Seleccione...</option>
      <?php for($i=6; $i<=20; $i+=2){
        echo '<option value="'.$i.'">'.$i.':00</option>';
      }   
      ?>
    </select>
  </div>
  <div class="col-lg-2 col-md-2">
    <label>Hora fin</label>
    <select required name="hora_fin[1]" class="form-control hora_fin">
      <option value="">Seleccione...</option>
      <?php for($i=8; $i<=22; $i+=2){
        echo '<option value="'.$i.'">'.$i.':00</option>';
      }   
      ?>
    </select>
  </div>
  <div class="col-lg-2 col-md-2">
    <label>Instructor</label>
    <select required name="par_identificacion[1]" class="form-control par_identificacion">
      <option value="">Seleccione...</option>
      <?php foreach($instructores as $val){
        echo '<option value="'.$val->par_identificacion.'">'.$val->par_nombres.' '.$val->par_apellidos.'</option>';
      }  
      ?>
    </select>
  </div>
  <div class="col-lg-2 col-md-2">
    <label>Ambiente</label>
    <select required name="pla_amb_id[1]" class="form-control pla_amb_id">
      <option value="">Seleccione...</option>
      <?php foreach($ambientes as $val){
        echo '<option value="'.$val->pla_amb_id.'">'.$val->pla_amb_descripcion.'</option>';
      }   
      ?>
    </select>
  </div>
  <div class="col-lg-2 col-md-2">
    <label>Actividades ?</label>
    <select class="form-control agregarActividad">
      <option value="">Seleccione...</option>
      <option value="SI">SI</option>
      <option value="NO">NO</option>
    </select>
  </div>
  <div class="col-lg-2 col-lg-push-4 col-md-2 col-md-push-4">
    <label>Acci&oacute;n</label>
    <a class="btn btn-success btn-xs agregarContenido form-control" >Agregar</a>
  </div>
  <div class="col-lg-2 col-lg-push-4 col-md-2 col-md-push-4">
    <label>Acci&oacute;n</label>
    <a class="btn btn-danger btn-xs eliminarContenido form-control">Eliminar</a>
  </div>
  <div style="display:none;" class="instructorActividades col-lg-12 col-md-12">
  </div>
</div>