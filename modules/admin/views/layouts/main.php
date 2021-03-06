<?php
    use \admin\Module as Admin;

    $user = Admin::getAdminUserData();
    $gravatar = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($user->email))).'?d='.urlencode('http://www.zephir.ch/files/rocky_460px_bw.jpg').'&s=40';

    $this->beginPage()
?>

<!DOCTYPE html>
<html ng-app="zaa" ng-controller="LayoutMenuController">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>LUYA // {{currentItem.alias}}</title>

        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php $this->head() ?>

        <script>
            var authToken = '<?=$user->getAuthToken();?>';
        </script>
    </head>

    <body>
        <?php $this->beginBody() ?>

        <!-- ANGULAR SCRIPTS -->

        <script type="text/ng-template" id="modal">
        <div class="modal" ng-show="!isModalHidden" style="z-index:999999">
            <div class="modal-content" ng-transclude></div>
        </div>
        </script>

        <script type="text/ng-template" id="storageFileUpload">
        <div class="row">
            <div class="input-field col s6">
                <input type="file" file-model="myFile" />
                <button ng-click="push()" type="button">Datei Hochladen</button>
            </div>
            <div class="input-field col s6">
                <button type="button" ng-click="openModal()">Show File manager</button>
                <modal is-modal-hidden="modal"><storage-file-manager ng-model="$parent.$parent.ngModel"/></modal>
            </div>
        </div>
        </script>

        <script type="text/ng-template" id="storageImageUpload">
        <h5>{{label}}</h5>
        <div class="row">
            <div class="col s12">
                <label>Filter Auswahl</label>
                <select name="filterId" ng-model="filterId" class="browser-default"><option value="0">Kein Filter</option><option ng-repeat="item in filters" value="{{ item.id }}">{{ item.name }} ({{ item.identifier }})</option></select>
            </div>
        </div>
        <storage-file-upload ng-model="fileId"></storage-file-upload>
        <div class="row">
            <div class="col s6">
                <button ng-click="push2()" type="button">Hochladen</button>
            </div>
            <div class="col s6">
                <div ng-show="imagesrc"><a href="{{imagesrc}}" target="_blank"><img ng-show="imagesrc" ng-src="{{imagesrc}}" height="100" /></a></div>
                <div ng-show="!imagesrc"><p>Sie müssen zuerst einen Filter und eine Datei auswählen und danach auf <strong>Hochladen</strong> klicken um das Bild anzuzeigen.</div>
            </div>
        </div>
        </script>

        <script type="text/ng-template" id="storageFileManager">
        <div class="row">
            <div class="col s6">
                <button type="button" class="btn">Datei Hochladen</button>
            </div>
            <div class="col s6">
                <button ng-repeat="crumb in breadcrumbs" type="button" style="margin-right:10px;" class="btn" ng-click="loadFolder(crumb.id)">{{crumb.name}}</button>
            </div>
            <div class="col s12">
                <ul class="collection">
                    <li ng-repeat="folder in folders" class="collection-item avatar  orange lighten-4">
                        <i class="mdi-file-folder circle"></i>
                        <span class="title"><strong>{{folder.name}}</strong></span>
                        <button class="secondary-content btn" ng-click="loadFolder(folder.id)"><i class="mdi-action-open-in-new"></i></button>
                    </li>
                    <li ng-repeat="file in files" class="collection-item avatar" ng-click="toggleSelection(file)" ng-class="{'is-active' : inSelection(file)}">
                        <i class="mdi-file-attachment circle"></i>
                        <span class="title">{{file.name_original}}</span>
                        <p><span ng-if="inSelection(file)">Ausgewählt</span></p>
                        <button class="secondary-content btn" type="button" ng-if="allowSelection == true" ng-click="selectFile(file)"><i class="mdi-content-send"></i></button>
                    </li>
                    <li ng-if="files.length == 0"  class="collection-item">
                        <span class="title"><strong>Ordner ist leer</strong></span>
                        <p>Es wurden noch keine Dateine in diesen Ordner hochgleaden.</p>
                    </li>
                </ul>
            </div>
        </div>
        </script>

        <!-- /ANGULAR SCRIPTS -->

        <div class="luya-container">

            <div class="navbar-fixed">
                <nav>
                    <div class="nav-wrapper blue">

                        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="mdi-navigation-menu"></i></a>

                        <ul class="left hide-on-med-and-down">
                            <li ng-repeat="item in items" ng-class="{'active' : isActive(item) }">
                                <a ng-click="click(item)" class="navbar__link"><i class="[ {{item.icon}} left ] navbar__icon"></i>{{item.alias}}</a>
                            </li>
                        </ul>
                        <ul class="right">
                            <li>
                                <a class="dropdown-button" data-hover="true" dropdown data-activates="userMenu"><i class="mdi-action-account-circle right"></i><strong><?php echo $user->email; ?></strong></a>
                            </li>
                        </ul>
                        <ul class="side-nav" id="mobile-demo">
                            <li ng-repeat="item in items" ng-class="{'active' : isActive(item) }">
                                <a ng-click="click(item)" class="navbar__link"><i class="[ {{item.icon}} left ] navbar__icon"></i>{{item.alias}}</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div> <!-- /navbar-fixed -->

            <!-- User dropdown, called by javascript -->
            <ul id="userMenu" class="dropdown-content">
                <li><a href="<?= \Yii::$app->urlManager->createUrl(['admin/default/logout']); ?>">Abmelden</a></li>
                <li><a href="#!">Einstellungen</a></li>
            </ul>
            <!-- /User dropdown -->

            <!-- ANGULAR-VIEW -->
            <div class="luya-container__angular-placeholder module-{{currentItem.moduleId}}" ui-view></div>
            <!-- /ANGULAR-VIEW -->

        </div> <!-- /.luya-container -->

        <?php $this->endBody() ?>

        <script type="text/javascript">$(".button-collapse").sideNav();</script>

    </body>

</html>

<?php $this->endPage() ?>