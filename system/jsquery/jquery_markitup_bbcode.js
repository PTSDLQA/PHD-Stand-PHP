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
      {name:'Цвет', openWith:'[color=[![Цвет]!]]', closeWith:'[/color]', dropMenu: [
          {name:'Yellow', openWith:'[color=yellow]', closeWith:'[/color]', className:"col1-1" },
          {name:'Orange', openWith:'[color=orange]', closeWith:'[/color]', className:"col1-2" },
          {name:'Red', openWith:'[color=red]', closeWith:'[/color]', className:"col1-3" },
          {name:'Blue', openWith:'[color=blue]', closeWith:'[/color]', className:"col2-1" },
          {name:'Purple', openWith:'[color=purple]', closeWith:'[/color]', className:"col2-2" },
          {name:'Green', openWith:'[color=green]', closeWith:'[/color]', className:"col2-3" },
          {name:'White', openWith:'[color=white]', closeWith:'[/color]', className:"col3-1" },
          {name:'Gray', openWith:'[color=gray]', closeWith:'[/color]', className:"col3-2" },
          {name:'Black', openWith:'[color=black]', closeWith:'[/color]', className:"col3-3" }
      ]},
      {name:'Размер', key:'S', openWith:'[size=[![Размер текста]!]]', closeWith:'[/size]', dropMenu :[
          {name:'Большой', openWith:'[size=200]', closeWith:'[/size]' },
          {name:'Средний', openWith:'[size=100]', closeWith:'[/size]' },
          {name:'Малый', openWith:'[size=50]', closeWith:'[/size]' }
      ]},
      {separator:'---------------' },
      {name:'Простой список', openWith:'[list]\n', closeWith:'\n[/list]'},
      {name:'Нумерованый список', openWith:'[list=[![Начиная с номера]!]]\n', closeWith:'\n[/list]'},
      {name:'Элемент списка', openWith:'[*] '},
      {separator:'---------------' },
      {name:'Цитата', openWith:'[quote]', closeWith:'[/quote]\n'},
      {name:'Код', openWith:'[code]', closeWith:'[/code]\n'},
      {separator:'---------------' },
      {name:'wink', openWith:' ;) '},
      {name:'smile', openWith:' :) '},
      {name:'tongue', openWith:' :P '},
      {name:'lol', openWith:' :lol: '},
      {name:'huh', openWith:' :huh: '},
      {name:'sad', openWith:' :( '},
      {name:'angry', openWith:' :angry: '},
      {name:'cool', openWith:' B) '},
      {name:'unsure', openWith:' :unsure: '},
      {name:'ohmy', openWith:' :o '},
      {name:'blink', openWith:' :blink: '},
      {name:'shok', openWith:' :shok: '},
   ]
}