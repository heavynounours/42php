<?php

class                   AdminController extends Controller {
    public              $methods = [];
	public 				$viewParams = [];
	
    public function     index($p) {
        Auth::mustBeLogged(true);
		
        $this->addMethods();
		$this->setupView();
        $this->handleRequests();
        return $this->showAdmin();
    }

	private function 	setupView() {
		$this->viewParams = [
			'content' => '',
			'selectedItem' => 'dashboard',
			'nav' => ''
		];
        Conf::set('page.js', []);
        Conf::set('page.css', []);

        // Redactor
        Conf::append('page.js', '/lib/redactor/redactor.js');
        Conf::append('page.js', '/lib/redactor/fontsize.js');
        Conf::append('page.js', '/lib/redactor/fullscreen.js');
        Conf::append('page.js', '/lib/redactor/imagemanager.js');
        Conf::append('page.js', '/lib/redactor/table.js');
        Conf::append('page.js', '/lib/redactor/textdirection.js');
        Conf::append('page.js', '/lib/redactor/video.js');
        Conf::append('page.js', '/lib/redactor/lang/'.Conf::get('lang').'.js');
        Conf::append('page.css', '/lib/redactor/redactor.css');

        // Pickadate
        Conf::append('page.js', '/lib/pickadate/picker.js');
        Conf::append('page.js', '/lib/pickadate/picker.date.js');
        Conf::append('page.js', '/lib/pickadate/picker.time.js');
        if (Conf::get('lang') != 'en')
            Conf::append('page.js', '/lib/pickadate/translations/'.Conf::get('lang').'_'.strtoupper(Conf::get('lang')).'.js');
        Conf::append('page.css', '/lib/pickadate/themes/default.css');
        Conf::append('page.css', '/lib/pickadate/themes/default.date.css');
        Conf::append('page.css', '/lib/pickadate/themes/default.time.css');


        Conf::append('page.css', 'https://fonts.googleapis.com/css?family=Roboto:400,300');
        Conf::append('page.css', '/lib/admin/style.css');


        Conf::append('page.js', '/lib/swipe/swipe.js');
        Conf::append('page.js', '/lib/admin/menu.js');

        Conf::set('page.title', _t("Administration"));
	}
	
	private function 	getNav() {
		$str = '';
		foreach ($this->methods as $k => $v) {
            if (!isset($v->params['hidden']) || !$v->params['hidden'])
                $str .= '<li'.($this->viewParams['selectedItem'] == $k ? ' class="active"' : '').'>
                    <a href="'.Argv::createUrl('admin').'?module='.$k.'">'.(isset($v->params['icon']) ? '<i class="'.$v->params['icon'].'"></i>' : '').(isset($v->params['title']) ? $v->params['title'] : ucfirst($k)).'</a>
                </li>';
		}
		return $str;
	}

    private function    addMethods() {
        // WYSIWYG Image Upload
        $this->methods['wysiwygImageUpload'] = new AdminTable([
            'mode' => 'standalone',
            'hidden' => true,
            'title' => '',
            'handler' => function() {
                $value = Upload::job('file', false, ['jpg', 'jpeg', 'png', 'gif', 'pjpeg']);
                $ret = [
                    'filelink' => !$value ? false : $value
                ];
                echo stripslashes(json_encode($ret));
                die();
            }
        ]);
        
        //updateTableOrder
        $this->methods['updateTableOrder'] = new AdminTable([
	        'mode' => 'standalone',
            'hidden' => true,
            'title' => '',
            'handler' => function() {
                $table = $_GET['tableName'];
                $field = $_GET['field'];
                $items = explode(',', $_GET['list']);
				foreach ($items as $o => $id) {
					Db::exec('UPDATE `'.$table.'` SET `'.$field.'`='.$o.' WHERE `id`='.$id);
				}
            }
        ]);
        
        // Dropzone image upload
        $this->methods['dzUpload'] = new AdminTable([
            'mode' => 'standalone',
            'hidden' => true,
            'title' => '',
            'handler' => function() {
                echo Upload::job('file', false, ['jpg', 'jpeg', 'png', 'gif', 'pjpeg']);
                die();
            }
        ]);

        // Dashboard
        $this->methods['dashboard'] = new AdminTable([
            'mode' => 'standalone',
            'title' => _t('Tableau de bord'),
            'icon' => 'fi-home',
            'handler' => function() {
                return View::partial('admin/dashboard');
            }
        ]);

        // Users
        $this->methods['users'] = new AdminTable([
            'mode' => 'auto',
            'table' => 'User',
            'title' => _t('Utilisateurs'),
            'item' => _t('un utilisateur'),
            'icon' => 'fi-torsos-all',
            'fields' => [
                'genre' => [
                    'type' => ['select', [
                        'M.',
                        'Mme.'
                    ]],
                    'title' => _t("Civilité")
                ],
                'firstname' => [
                    'type' => 'text',
                    'title' => _t("Prénom")
                ],
                'lastname' => [
                    'type' => 'text',
                    'title' => _t("Nom")
                ],
                'email' => [
                    'type' => 'email',
                    'unique' => true,
                    'title' => _t("Adresse e-mail")
                ],
                'password' => [
                    'type' => 'password',
                    'title' => _t("Mot de passe")
                ],
                'photo' => [
                    'type' => ['image', '256x256'],
                    'title' => 'Photo de profil'
                ],
                'registered' => [
                    'type' => 'datetime',
                    'default' => date('Y-m-d H:i:s'),
                    'title' => _t("Date d'inscription")
                ],
                'admin' => [
                    'type' => 'bool',
                    'default' => false,
                    'title' => _t("Administrateur")
                ],
                'slug' => [
                    'type' => 'text',
                    'unique' => true,
                    'title' => _t("Référence"),
                    'ifEmpty' => function() {
                        $base = Text::slug($_POST['firstname']).'-'.Text::slug($_POST['lastname']);
                        $suffix = '';
                        $res = true;
                        while ($res) {
                            $res = Db::getInstance()->User->findOne([
                                'slug' => $base . $suffix
                            ]);
                            if ($res)
                                $suffix = $suffix == '' ? 1 : intval($suffix) + 1;
                        }
                        return $base . $suffix;
                    }
                ],
                'lang' => [
                    'type' => ['select', i18n::$__acceptedLanguages],
                    'title' => _t("Langue")
                ]
            ],
            'header' => 'photo|email|admin'
        ]);


        // Blog
        $categories = Db::query('SELECT id, title FROM Category WHERE lang='.Db::quote(Conf::get('lang')).' ORDER BY title');
        $cats = [];
        foreach ($categories as $category)
            $cats[$category['id']] = $category['title'];
        if (!sizeof($cats)) {
            $c = new Category();
            $c->lang = Conf::get('lang');
            $c->title = _t("Par défaut");
            $c->save();
            $cats[$c->id] = $c->title;
        }
        $this->methods['articles'] = new AdminTable([
            'mode' => 'auto',
            'table' => 'Article',
            'title' => _t('Articles'),
            'item' => _t('un article'),
            'icon' => 'fi-rss',
            'fields' => [
                'title' => [
                    'type' => 'text',
                    'title' => _t("Titre")
                ],
                'category' => [
                    'type' => ['select', $cats],
                    'title' => _t("Catégorie")
                ],
                'intro' => [
                    'type' => 'html',
                    'title' => _t("Introduction")
                ],
                'content' => [
                    'type' => 'html',
                    'title' => _t("Contenu")
                ],
                'image' => [
                    'type' => ['image', '2048x2048'],
                    'title' => _t("Photo de couverture")
                ],
                'keywords' => [
                    'type' => 'text',
                    'title' => _t('Mots-clés')
                ],
                'date' => [
                    'type' => 'date',
                    'default' => date('Y-m-d'),
                    'title' => _t("Date de parution")
                ],
                'author' => [
                    'type' => 'hidden',
                    'default' => Session::get('user.id'),
                    'title' => _t("Auteur")
                ],
                'slug' => [
                    'type' => 'text',
                    'unique' => true,
                    'title' => _t("Référence"),
                    'ifEmpty' => function() {
                        $base = Text::slug($_POST['title']);
                        $suffix = '';
                        $res = true;
                        while ($res) {
                            $res = Db::getInstance()->Article->findOne([
                                'slug' => $base . $suffix
                            ]);
                            if ($res)
                                $suffix = $suffix == '' ? 1 : intval($suffix) + 1;
                        }
                        return $base . $suffix;
                    }
                ],
                'lang' => [
                    'type' => 'hidden',
                    'default' => Conf::get('lang'),
                    'title' => _t("Langue")
                ],
                'enabled' => [
                    'type' => 'bool',
                    'default' => true,
                    'title' => _t("Publié")
                ]
            ],
            'header' => 'image|title|date|enabled',
            'restrict' => [
                'lang' => Conf::get('lang')
            ]
        ]);

        $this->methods['categories'] = new AdminTable([
            'mode' => 'auto',
            'table' => 'Category',
            'title' => _t('Catégories'),
            'item' => _t('une catégorie'),
            'icon' => 'fi-folder',
            'fields' => [
                'lang' => [
                    'type' => 'hidden',
                    'default' => Conf::get('lang'),
                    'title' => _t("Langue")
                ],
                'title' => [
                    'type' => 'text',
                    'title' => _t("Titre")
                ],
                'description' => [
                    'type' => 'text',
                    'title' => _t("Description")
                ],
                'keywords' => [
                    'type' => 'text',
                    'title' => _t("Mots-clés")
                ],
                'slug' => [
                    'type' => 'text',
                    'unique' => true,
                    'title' => _t("Référence"),
                    'ifEmpty' => function() {
                        $base = Text::slug($_POST['title']);
                        $suffix = '';
                        $res = true;
                        while ($res) {
                            $res = Db::getInstance()->Article->findOne([
                                'slug' => $base . $suffix
                            ]);
                            if ($res)
                                $suffix = $suffix == '' ? 1 : intval($suffix) + 1;
                        }
                        return $base . $suffix;
                    }
                ]
            ],
            'header' => 'title',
            'restrict' => [
                'lang' => Conf::get('lang')
            ]
        ]);


        // Pages
        $viewFiles = [];
        foreach (Dir::read(ROOT.'/views', true, '*.php') as $file) {
            $file = str_replace('\\', '/', $file);
            $file = str_replace([str_replace('\\', '/', ROOT).'/views/', '.php'], '', $file);
            if (!preg_match('#^(admin|system)/#', $file))
                $viewFiles[] = $file;
        }
        $this->methods['pages'] = new AdminTable([
            'mode' => 'auto',
            'table' => 'Page',
            'title' => _t('Pages'),
            'item' => _t('une page'),
            'icon' => 'fi-page-filled',
            'fields' => [
                'path' => [
                    'type' => 'text',
                    'title' => _t("URL")
                ],
                'file' => [
                    'type' => ['select', $viewFiles],
                    'title' => _t("Fichier de vue")
                ],
                'title' => [
                    'type' => 'text',
                    'title' => _t("Titre")
                ],
                'description' => [
                    'type' => 'text',
                    'title' => _t("Description")
                ],
                'keywords' => [
                    'type' => 'text',
                    'title' => _t("Mots-clés")
                ],
                'image' => [
                    'type' => ['image', '512x512'],
                    'title' => _t("Image de partage")
                ],
            ],
            'header' => 'path|file'
        ]);

        // Dashboard
        $this->methods['views'] = new AdminTable([
            'mode' => 'standalone',
            'title' => _t('Vues'),
            'icon' => 'fi-layout',
            'handler' => function() {
                $viewFiles = [];
                foreach (Dir::read(ROOT.'/views', true, '*.php') as $file) {
                    $file = str_replace('\\', '/', $file);
                    $file = str_replace([str_replace('\\', '/', ROOT).'/views/', '.php'], '', $file);
                    if (!preg_match('#^(admin|system)/#', $file))
                        $viewFiles[] = $file;
                }

                if (isset($_GET['file']) && in_array($_GET['file'], $viewFiles)) {
                    if (isset($_POST['content'])) {
                        file_put_contents(ROOT . '/views/' . $_GET['file'] . '.php', $_POST['content']);
                        Redirect::http(Conf::get('admin.url'));
                    }
                    $content = file_get_contents(ROOT . '/views/' . $_GET['file'] . '.php');
                    return View::partial('admin/views/editor', [
                        'content' => htmlentities($content)
                    ]);
                }

                return View::partial('admin/views/list', [
                    'files' => $viewFiles
                ]);
            }
        ]);

        // Dashboard
        $this->methods['i18n'] = new AdminTable([
            'mode' => 'standalone',
            'title' => _t('Langues'),
            'icon' => 'fi-flag',
            'handler' => function() {
                $langFiles = [];
                foreach (Dir::read(ROOT.'/i18n', true, '*.json') as $file) {
                    $file = str_replace('\\', '/', $file);
                    $file = str_replace([str_replace('\\', '/', ROOT).'/i18n/', '.json'], '', $file);
                    $langFiles[] = $file;
                }

                if (isset($_GET['file']) && in_array($_GET['file'], $langFiles)) {
                    if (isset($_POST['content'])) {
                        $n = [];
                        foreach ($_POST['content'] as $k => $v)
                            $n[base64_decode($k)] = $v;
                        file_put_contents(ROOT . '/i18n/' . $_GET['file'] . '.json', json_encode($n, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        Redirect::http(Conf::get('admin.url'));
                    }
                    $content = json_decode(file_get_contents(ROOT . '/i18n/' . $_GET['file'] . '.json'), true);
                    return View::partial('admin/i18n/editor', [
                        'content' => $content
                    ]);
                }

                return View::partial('admin/i18n/list', [
                    'files' => $langFiles
                ]);
            }
        ]);











        // AB testing dashboard
        $this->methods['ab'] = new AdminTable([
            'mode' => 'standalone',
            'title' => _t('Tests A/B'),
            'icon' => 'fi-page-multiple',
            'handler' => function() {
                $pages = Db::query('SELECT path, pagehash, count(id) as nb FROM `ABPageView` GROUP BY `pagehash` ORDER BY `id`');
                return View::partial('admin/ab/list', [
                    'pages' => $pages
                ]);
            }
        ]);
        $this->methods['ab-view'] = new AdminTable([
            'mode' => 'standalone',
            'title' => _t('Tests A/B'),
            'hidden' => true,
            'icon' => 'fi-page-multiple',
            'handler' => function() {
                if (!isset($_GET['pagehash']))
                    Redirect::http(Argv::createUrl('admin').'?module=ab');
                $pagehash = $_GET['pagehash'];

                $viewParams = [];

                $totalviews = Db::get('SELECT COUNT(id) as nb, `path` FROM `ABPageView` WHERE `pagehash`='.Db::quote($pagehash));
                $totalclicks = Db::get('SELECT COUNT(id) as nb, `path` FROM `ABPageView` WHERE `clicked`=1 AND `pagehash`='.Db::quote($pagehash));
                $views = Db::query('SELECT COUNT(id) as nb, `file` FROM `ABPageView` WHERE `pagehash`='.Db::quote($pagehash).' GROUP BY `file` ORDER BY `nb` DESC');

                $viewParams['url'] = $totalviews['path'];
                $viewParams['totalviews'] = intval($totalviews['nb']);
                $viewParams['totalclicks'] = intval($totalclicks['nb']);

                $viewList = [];
                foreach ($views as $view) {
                    $data = [
                        'file' => $view['file'],
                        'views' => intval($view['nb']),
                        'clicks' => []
                    ];
                    $totalclicks = Db::get('SELECT COUNT(id) as nb FROM `ABPageView` WHERE `clicked`=1 AND `pagehash`='.Db::quote($pagehash).' AND `file`='.Db::quote($view['file']));
                    $clicks = Db::query('SELECT COUNT(id) as nb, param FROM `ABPageView` WHERE `clicked`=1 AND `pagehash`='.Db::quote($pagehash).' AND `file`='.Db::quote($view['file']).' GROUP BY `param` ORDER BY `nb` DESC');

                    $data['totalclicks'] = intval($totalclicks['nb']);
                    foreach ($clicks as $click) {
                        $data['clicks'][] = [
                            'nb' => intval($click['nb']),
                            'param' => $click['param']
                        ];
                    }
                    $viewList[] = $data;
                }
                $viewParams['list'] = $viewList;
                $viewParams['viewlist'] = Db::query('SELECT * FROM `ABPageView` WHERE `pagehash`='.Db::quote($pagehash));

                return View::partial('admin/ab/page', $viewParams);
            }
        ]);
















    }

    private function    handleRequests() {
		$toLoad = 'dashboard';
		if (isset($_GET['module']))
			$toLoad = $_GET['module'];

		if (!isset($this->methods[$toLoad])) {
			$this->viewParams['content'] = View::partial('404');
		} else {
            Conf::set('admin.module', $toLoad);
            Conf::set('admin.moduleTitle', $this->methods[$toLoad]->params['title']);
            Conf::set('admin.url', Argv::createUrl('admin').'?module='.$toLoad);
			$this->viewParams['content'] = $this->methods[$toLoad]->render();
			$this->viewParams['selectedItem'] = $toLoad;
		}
    }

    private function    showAdmin() {
	    $this->viewParams['nav'] = $this->getNav();
		return View::render('admin/index', $this->viewParams);
    }
}

?>