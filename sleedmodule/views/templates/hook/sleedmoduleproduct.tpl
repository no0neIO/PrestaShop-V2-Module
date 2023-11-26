{if (isset($feature1) and !empty($feature1)) or (isset($feature2) and !empty($feature2)) or (isset($feature3) and !empty($feature3))}
  <div id="sleed_product_footer" class="block">
    <h4>Features</h4>
    <div class="container">
      <div class="row p-5">
        <div class="col-xs-12 col-sm-6 col-md-4 text-center">{$feature1|default:''}</div>
        <div class="col-xs-12 col-sm-6 col-md-4 text-center">{$feature2|default:''}</div>
        <div class="col-xs-12 col-sm-6 col-md-4 text-center">{$feature3|default:''}</div>
      </div>
    </div>
  </div>
{/if}