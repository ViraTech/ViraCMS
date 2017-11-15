<ul class="hide"<?= isset($languageID) ? ' data-language-id="' . $languageID . '"' : '' ?><?= isset($id) ? ' id="' . $id . '"' : '' ?>>
  <li data-attribute="title"><?= $l10n ? $l10n->title : '' ?></li>
  <li data-attribute="caption"><?= $l10n ? $l10n->caption : '' ?></li>
  <li data-attribute="pageID"><?= $l10n ? $l10n->pageID : '' ?></li>
  <li data-attribute="url"><?= $l10n ? $l10n->url : '' ?></li>
</ul>
