<header>
  <h1 class="hidden">Smart_Book_Shelf_Users</h1>
  <!-- bootstrapを使ったヘッダー作成 -->
  <div class="container">
      <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
          <div class="navbar-header">
              <a class="navbar-brand" href="#">  <i class="fas fa-book-open"></i>Smart Book Shelf</a>
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#gnav">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
          </div>
          <div class="collapse navbar-collapse" id="gnav">
              <ul class="nav navbar-nav navbar-right">
                <?php if ($_SESSION["user"]["id"]==1){ ?>
                  <li><a href="../admin/main.php"><?php echo $_SESSION["user"]["name"] ?></a></li>
                  <li><a href="?logout=1">ログアウト</a></li>
                <?php }else{ ?>
                  <li><a href="main.php"><?php echo $_SESSION["user"]["name"] ?></a></li>
                  <li><a href="review_index.php">投稿一覧</a></li>
                  <li><a href="?logout=1">ログアウト</a></li>
                <?php }; ?>
              </ul>
            </div><!-- /.navbar-collapse -->
          </div>
      </nav>
  </div><!-- /container -->
</header>
