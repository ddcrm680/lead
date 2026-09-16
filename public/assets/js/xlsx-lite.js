(function(global){
  'use strict';
  function xmlEscape(value){return String(value??'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&apos;'}[c]})}
  function colIndex(ref){var letters=(ref.match(/[A-Z]+/i)||['A'])[0].toUpperCase(),n=0;for(var i=0;i<letters.length;i++)n=n*26+letters.charCodeAt(i)-64;return n-1}
  function colName(index){var name='';index+=1;while(index){var r=(index-1)%26;name=String.fromCharCode(65+r)+name;index=Math.floor((index-1)/26)}return name}
  function parseXml(text){return new DOMParser().parseFromString(text,'application/xml')}
  async function read(buffer){
    if(!global.JSZip)throw new Error('JSZip is unavailable');
    var zip=await JSZip.loadAsync(buffer),shared=[];
    var sharedFile=zip.file('xl/sharedStrings.xml');
    if(sharedFile){var sx=parseXml(await sharedFile.async('string'));shared=[].slice.call(sx.getElementsByTagName('si')).map(function(si){return [].slice.call(si.getElementsByTagName('t')).map(function(t){return t.textContent||''}).join('')})}
    var sheetPath='xl/worksheets/sheet1.xml';
    var workbook=zip.file('xl/workbook.xml'),rels=zip.file('xl/_rels/workbook.xml.rels');
    if(workbook&&rels){var wx=parseXml(await workbook.async('string')),rx=parseXml(await rels.async('string')),sheet=wx.getElementsByTagName('sheet')[0];if(sheet){var rid=sheet.getAttribute('r:id')||sheet.getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships','id');var relation=[].slice.call(rx.getElementsByTagName('Relationship')).find(function(r){return r.getAttribute('Id')===rid});if(relation){var target=relation.getAttribute('Target').replace(/^\//,'');sheetPath=target.indexOf('xl/')===0?target:'xl/'+target.replace(/^\.\//,'')}}}
    var sheetFile=zip.file(sheetPath);if(!sheetFile)throw new Error('Worksheet not found');
    var doc=parseXml(await sheetFile.async('string')),rows=[].slice.call(doc.getElementsByTagName('row')).map(function(row){var out=[];[].slice.call(row.getElementsByTagName('c')).forEach(function(c){var idx=colIndex(c.getAttribute('r')||'A1'),type=c.getAttribute('t'),value='';if(type==='inlineStr'){var it=c.getElementsByTagName('t')[0];value=it?it.textContent:''}else{var v=c.getElementsByTagName('v')[0];value=v?v.textContent:'';if(type==='s')value=shared[Number(value)]||'';else if(type==='b')value=value==='1'?'TRUE':'FALSE'}out[idx]=value});return out});
    if(rows.length<2)return [];
    var headers=rows.shift().map(function(h){return String(h||'').trim()});
    return rows.filter(function(r){return r.some(function(v){return String(v||'').trim()})}).map(function(row){var obj={};headers.forEach(function(h,i){if(h)obj[h]=row[i]??''});return obj});
  }
  async function write(rows,filename){
    if(!global.JSZip)throw new Error('JSZip is unavailable');
    rows=rows||[];var headers=Object.keys(rows[0]||{}),all=[headers].concat(rows.map(function(r){return headers.map(function(h){return r[h]})}));
    var sheetRows=all.map(function(row,r){var cells=row.map(function(value,c){var ref=colName(c)+(r+1);if(typeof value==='number'&&isFinite(value))return '<c r="'+ref+'"><v>'+value+'</v></c>';return '<c r="'+ref+'" t="inlineStr"><is><t xml:space="preserve">'+xmlEscape(value)+'</t></is></c>'}).join('');return '<row r="'+(r+1)+'">'+cells+'</row>'}).join('');
    var zip=new JSZip();
    zip.file('[Content_Types].xml','<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>');
    zip.folder('_rels').file('.rels','<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>');
    zip.folder('xl').file('workbook.xml','<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Leads" sheetId="1" r:id="rId1"/></sheets></workbook>');
    zip.folder('xl').folder('_rels').file('workbook.xml.rels','<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
    zip.folder('xl').folder('worksheets').file('sheet1.xml','<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'+sheetRows+'</sheetData></worksheet>');
    zip.folder('docProps').file('core.xml','<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/"><dc:title>Lead CRM Dashboard</dc:title><dc:creator>Lead CRM</dc:creator><dcterms:created xsi:type="dcterms:W3CDTF" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'+new Date().toISOString()+'</dcterms:created></cp:coreProperties>');
    zip.folder('docProps').file('app.xml','<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>LEAD CRM</Application></Properties>');
    var blob=await zip.generateAsync({type:'blob',compression:'DEFLATE'}),url=URL.createObjectURL(blob),a=document.createElement('a');a.href=url;a.download=filename||'leads.xlsx';document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(url)
  }
  global.SimpleXLSX={read:read,write:write};
})(window);
