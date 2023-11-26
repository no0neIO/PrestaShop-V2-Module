<div class="form-group">
  <form>
    <label for="feature1">Feature 1:</label><br>
    <input type="text" id="feature1" name="feature1" placeholder="Feature 1"
           value={$feature1|default:''}><br>
    <label for="feature2">Feature 2: </label><br>
    <input type="text" id="feature2" name="feature2" placeholder="Feature 2"
           value={$feature2|default:''}><br>
    <label for="feature3">Feature 3:</label><br>
    <input type="text" id="feature3" name="feature3" placeholder="Feature 3"
           value={$feature3|default:''}><br>
  </form>
</div>
<div class="panel-footer">
  <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default"><i
            class="process-icon-cancel"></i> {l s='Cancel'}</a>
  <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l
      s='Save'}</button>
  <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i
            class="process-icon-save"></i> {l s='Save and stay'} </button>
</div>