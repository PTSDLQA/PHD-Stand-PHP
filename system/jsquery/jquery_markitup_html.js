// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
mySettings = {
    nameSpace:       "html", // Useful to prevent multi-instances CSS conflict
    onShiftEnter:    {keepDefault:false, replaceWith:'<br />\n'},
    onCtrlEnter:     {keepDefault:false, openWith:'\n<p>', closeWith:'</p>\n'},
    onTab:           {keepDefault:false, openWith:'     '},
    markupSet:  [
        {name:'Заголовок 3', key:'3', openWith:'<h3(!( class="[![Class]!]")!)>', closeWith:'</h3>', placeHolder:'Текст заголовка...' },
        {name:'Заголовок 4', key:'4', openWith:'<h4(!( class="[![Class]!]")!)>', closeWith:'</h4>', placeHolder:'Текст заголовка...' },
        {name:'Заголовок 5', key:'5', openWith:'<h5(!( class="[![Class]!]")!)>', closeWith:'</h5>', placeHolder:'Текст заголовка...' },
        {name:'Заголовок 6', key:'6', openWith:'<h6(!( class="[![Class]!]")!)>', closeWith:'</h6>', placeHolder:'Текст заголовка...' },
        {name:'Абзац', openWith:'<p(!( class="[![Class]!]")!)>', closeWith:'</p>'  },
        {separator:'---------------' },
        {name:'Жирный', key:'B', openWith:'<strong>', closeWith:'</strong>' },
        {name:'Курсив', key:'I', openWith:'<em>', closeWith:'</em>'  },
        {name:'Зачеркнутый', key:'S', openWith:'<del>', closeWith:'</del>' },
        {separator:'---------------' },
        {name:'Простой список', openWith:'<ul>\n', closeWith:'</ul>\n' },
        {name:'Нумерованный список', openWith:'<ol>\n', closeWith:'</ol>\n' },
        {name:'Элемент списка', openWith:'<li>', closeWith:'</li>' },
        {separator:'---------------' },
        {name:'Картинка', key:'P', replaceWith:'<img src="[![URL картинки:!:http://]!]" alt="[![Описание картинки]!]" />' },
		{name:'Ссылка', key:'L', openWith:'<a href="[![URL ссылки:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Текст ссылки...' },
		{name:'Видео', openWith:'<video>[![URL видео (youtube, rutube):!:http://]!]', closeWith:'</video>' },
		{name:'Код', openWith:'<code>', closeWith:'</code>\n'},
        {separator:'---------------' },
        {name:'Кат', openWith:'<cut>' },
    ]
}