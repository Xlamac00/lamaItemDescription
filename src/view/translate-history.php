<tr>
  <th scope="row"><?php echo date('j. m. Y', strtotime($date)) ?></th>
  <td><i><?php echo $title ?></i><br><?php echo $text ?></td>
  <td><i class="fas fa-user"></i><?php echo $author ?></td>
  <td>
      <button class="btn btn-link text-danger history-delete ml-0" title="Delete change" value="<?php echo $hid ?>">
          <i class="fas fa-trash"></i></button>
      <i class="fas fa-cog fa-spin text-primary ml-3 mt-2 d-none" id="modal-spinner-<?php echo $hid ?>"></i>
  </td>
</tr>
<input type="hidden" value="<?php echo $bid ?>" id="modal-id-<?php echo $hid ?>">