<?php
  //seguridad
	if(Session::getVariable('autentication')!= sha1(Session::getVariable('username')))
		header('Location:index?c=login&m=show');
?>
<main id="main" class="center">
  <div><?= $user ?? '' ?></div>
  <table id="table">
    <thead>
      <tr><th id="order-id" data-sort="<?= ($column ?? '' == 'id') ? $sort : 'DESC' ?>" class="order-column">Id</th>
        <th id="order-title" data-sort="<?= ($column ?? '' == 'title') ? $sort : 'DESC' ?>" class="order-column">Título</th>
        <th id="order-artist_name" data-sort="<?= ($column  ?? ''== 'artist_name') ? $sort : 'DESC' ?>" class="order-column">Artista</th>
        <th id="order-duration" data-sort="<?= ($column ?? '' == 'duration') ? $sort : 'DESC' ?>" class="order-column">Duración</th>
        <th id="order-release_date" data-sort="<?= ($column ?? '' == 'release_date') ? $sort : 'DESC' ?>" class="order-column">Imagen</th>
        <th>Action</th></tr>
    </thead>
    <tbody>
      <?php $count = 0; ?>

      <?php foreach ($result as $row): ?>
        <?php if ($row->duration != 0): ?>
        <?php ++$count; ?>
      <tr id="row<?= $row->mbid ?>">
        <td><?= $count ?></td><td><?= $row->name ?></td><td><?= $row->artist->name ?></td><td><?= ($row->duration) ?></td>
        <td><a href="<?= $row->url ?>"><img height="30" width="30" src="<?= $row->image[0]->text ?>" /></a></td>
        <td>
          <?php $param = 'id=' . $count . '&amp;title=' . $row->name . '&amp;artist_name=' . $row->artist->name . '&amp;duration=' . ($row->duration * 60) . '&amp;cd_cover=' . ''; ?>
          <a class="edit" data-param="<?= $param ?>" href="index.php?for_edit=true&amp;<?= $param ?>"></a>
          <a class="delete" data-id="<?= $count ?>" href="index.php?for_delete=true&amp;id=<?= $count ?>"></a>
        </td>
      </tr>
      <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
  </table>
  <div id="edit-form-dialog" title="Edit Row">
    <form id="edit-form">
      <div>
        <label>Id: </label>
        <input id="edit-id" name="id" readonly="readonly" type="text" />
      </div>
      <div>
        <label>Title: </label>
        <input id="edit-title" name="title" type="text" />
      </div>
      <div>
        <label>Duration: </label>
        <input id="edit-duration" name="duration" type="text" />
      </div>
      <input type="submit" value="Modify" />
      <!--<select id="edit-artist" name="id">

      </select>-->
    </form>
  </div>
  <div id="delete-confirmation" title="Delete song">
    <p>Are you sure you want to delete the song?</p>
  </div>
  <a id="add" href="index.php?for_insert=true">Add row <span></span></a>
  <form id="add-form">
    <div>
      <label>Title: </label>
      <input id="add-title" name="title" required="required" type="text" />
    </div>
    <div>
      <label>Duration (sec): </label>
      <input id="add-duration" name="duration" min="1" max="600" type="number" />
    </div>
    <div>
      <label>Artist name: </label>
      <select id="add-artist_id" name="artist_name" required="required" />
        <option></option>
        <option>Añadir nuevo</option>
        <option>------------</option>
      </select>
    </div>
    <input type="submit" value="Insert" />
  </form>
</main>
