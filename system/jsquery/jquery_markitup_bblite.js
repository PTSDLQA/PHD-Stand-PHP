// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
  nameSpace:          "bbcode", // Useful to prevent multi-instances CSS conflict
  previewParserPath:  "",
  markupSet: [
      {name:'Жирный', key:'B', openWith:'[b]', closeWith:'[/b]'},
      {name:'Курсив', key:'I', openWith:'[i]', closeWith:'[/i]'},
      {name:'Подчеркнутый', key:'U', openWith:'[u]', closeWith:'[/u]'},
      {separator:'---------------' },
      {name:'Картинка', key:'P', replaceWith:'[img][![URL картинки]!][/img]'},
      {name:'Ссылка', key:'L', openWith:'[url=[![URL ссылки]!]]', closeWith:'[/url]', placeHolder:'Текст ссылки'},
      {separator:'---------------' },
      {name:'Простой список', openWith:'[list]\n', closeWith:'\n[/list]'},
      {name:'Нумерованый список', openWith:'[list=[![Начиная с номера]!]]\n', closeWith:'\n[/list]'},
      {name:'Элемент списка', openWith:'[*] '},
   ]
}