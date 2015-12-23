<?php
//validate parameters
if ( !isset($accessPageId) ) {
    echo "<b>Render paginator: missing parameter accessPageId to access pageId value</b>";
    return;
}
if ( !isset($accessPagesCount) ) {
    echo "<b>Render paginator: missing parameter accessPagesCount to access pagesCount value</b>";
    return;
}

if ( !isset($accessFind) ) {
    echo "<b>Render paginator: missing parameter accessFind to access find() function</b>";
    return;
}
?>
<span class="paginator" ng-show="<?= $accessPagesCount ?> > 0" 
      ng-init="steps = [ - 5, - 4, - 3, - 2, - 1, 0, 1, 2, 3, 4, 5 ]">
    <a ng-repeat="step in steps" 
       ng-click="$parent.<?= $accessPageId ?> = $parent.<?= $accessPageId ?> + step;
                   $parent.<?= $accessFind ?>"
       class="{{step == 0 ? 'active' : ''}}"  
       ng-show="$parent.<?= $accessPageId ?> + step >= 0 && $parent.<?= $accessPageId ?> + step < $parent.<?= $accessPagesCount ?>">
        {{$parent.<?= $accessPageId ?> + step + 1}}
    </a>
</span>