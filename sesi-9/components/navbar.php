<?php
function renderNavbar(string $activeMenu = 'home', string $basePath = ''): void
{
	?>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container">
			<a class="navbar-brand" href="<?php echo htmlspecialchars($basePath . 'home.php', ENT_QUOTES, 'UTF-8'); ?>">Ecommerce</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="mainNavbar">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item">
						<a class="nav-link <?php echo $activeMenu === 'home' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($basePath . 'home.php', ENT_QUOTES, 'UTF-8'); ?>">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo $activeMenu === 'create-product' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($basePath . 'admin/products/create.php', ENT_QUOTES, 'UTF-8'); ?>">Tambah Product</a>
					</li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeMenu === 'about' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($basePath . 'about.php', ENT_QUOTES, 'UTF-8'); ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeMenu === 'cart' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($basePath . 'cart.php', ENT_QUOTES, 'UTF-8'); ?>">Keranjang</a>
				</ul>
			</div>
		</div>
	</nav>
	<?php
}

