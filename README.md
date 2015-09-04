# twig-declension

Фильтр для twig

* Позволяет управлять списком склоняемых слов и множественных форм
* Подключается как расширение к шаблонизатору twig
* Применяется к строке как фильтр в шаблоне
* Находит склоняемое слово в предварительно наполненной таблице и возвращает требуемую форму
* В случае отсутствия соответствующей записи в БД или при пустом склонении возвращает исходную строку или именительный падеж 

1) Установка
----------------------------------
    Добавить в composer.json
    ```json
    # composer.json
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:bubnovKelnik/twig-declension.git"
        }
    ]
    ```

    Выполнить:
    ```sh
    composer require bubnovKelnik/twig-declension:dev-master
    ```

    Добавить бандл в конфигурацию AppKernel
    ```php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new BubnovKelnik\TwigDeclensionBundle\BubnovKelnikTwigDeclensionBundle(),
            // ...
        );
    }
    ```

    Обновить базу данных
    ```sh
    app/console doctrine:schema:update
    ```
    
    Настроить config.yml
    ```yml
    # app/config.yml
    # Укажите путь к главному шаблону в Symfony-формате
    # Нужно для страниц управления спряжениями
    twig:
        globals:
            html_base_template: 'AppMyBundle::base.html.twig'
    ```

    Настроить routing.yml
    ```yml
    # app/routing.yml
    bubnovKelnik_twig-declension:
        resource: "@BubnovKelnikTwigDeclensionBundle/Resources/config/routing/routing.yml"
    ```

    Если на странице используется множество склонений, имеет смысл сразу загрузить все формы
    Для этого переопределите сервис-расширение в app/config.yml, передав true в качестве второго аргумента конструктора
    ```yml
    # app/config.yml
    twig.declension:
            class: BubnovKelnik\TwigDeclensionBundle\Twig\Extension\TwigDeclensionExtension
            arguments: [@doctrine.orm.entity_manager, true]
            tags:
                - { name: twig.extension }
    ```

2) Использование
-------------------------------------
    Добавить ссылку в административной панели или меню
    ```twig
    {# Ваш шаблон меню/панели #}
    <a href="{{ path('admin_twig_declension') }}">Слонения</a>
    ```

    **Внимание! В контроллере используется контроль доступа. Пользователь должен обладать ролью ROLE_ADMIN**

    Создать необходимые записи в административном интерфейсе
    В данном примере мы создали запись "яблоко" и заполнили все падежи и множественные формы

    Склонение:
    ```twig
    {# Ваш шаблон #}
    Ньютон получил по голове {{ 'яблоко' | declension('abl') }}
    {# Получится 'Ньютон получил по голове  яблоком' #}
    ```

    Множественное число:
    ```twig
    {# Ваш шаблон #}
    В ящике лежат {{ 'яблоко' | declension('inf_multi') }}
    {# Получится 'В ящике лежат яблоки' #}
    ```

    Множественные формы:
    ```twig
    {# Ваш шаблон #}
    У меня в кармане 12 {{ 'яблоко' | declension('plural', 12) }}
    {# Получится 'У меня в кармане 12 яблок' #}
    ```

3) Список ключей и падежей
-------------------------------------
    * inf         - именительный падеж
    * inf_multi   - именительный падеж множественного числа
    * gen         - родительный падеж
    * gen_multi   - родительный падеж множественного числа
    * dat         - дательный падеж
    * acc         - винительный падеж
    * abl         - творительный падеж
    * pre         - предложный падеж
    * plural      - множественные формы


4) Автоматическое заполнение форм слова
-------------------------------------
    На странице добавления/обновления записи можно настроить автоматическое заполнение форм слова.

    Для генерации url к контроллеру используется FOS\JsRoutingBundle
    Подключите его в app/AppKernel.php (скорее всего он там уже есть)
    ```php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            // ...
        );
    }
    ```

    Подключите в html js-ассет 'bundles/fosjsrouting/js/router.js' и 'bundles/bubnovkelniktwigdeclension/js/auto-declension.js'
    ```html
    <script type="text/javascript" src="{{ asset('bundles/fosjsrouting/js/router.js') }}></script>
    <script type="text/javascript" src="{{ asset('bundles/bubnovkelniktwigdeclension/js/auto-declension.js') }}></script>
    ```

    или

    ```html
    {% javascripts
        ...
        'bundles/fosjsrouting/js/router.js'
        'bundles/bubnovkelniktwigdeclension/js/auto-declension.js'
        ...
        output='compiled/compiled.js'
    %}
    <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}
    ```

    Установите ассеты из бандла
    ```sh
    app/console assets:install
    ```

    Вы также можете настроить автоматическое создание форм слов, запрашиваемых в twig.
    Для этого переопределите сервис-расширение в app/config.yml, передав true в качестве третьего аргумента конструктора
    ```yml
    # app/config.yml
    twig.declension:
            class: BubnovKelnik\TwigDeclensionBundle\Twig\Extension\TwigDeclensionExtension
            arguments: [@doctrine.orm.entity_manager, null, true]
            tags:
                - { name: twig.extension }
    ```