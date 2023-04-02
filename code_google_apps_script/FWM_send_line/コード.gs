// スプレットシートからLINE_IDを取得
function get_line_id(user_id){
  // スプレットシートの取得
  const db_id = PropertiesService.getScriptProperties().getProperty("database_id");
  let table_member = SpreadsheetApp.openById(db_id).getSheetByName('table_member');
  let last_row = table_member.getLastRow();
  let user_data = table_member.getRange(2,1,last_row,2).getValues();
  // ユーザーIDがあれば戻す
  for(let i=0;i<user_data.length;i++){
    if(user_data[i][1] === user_id){
      return user_data[i][0];
    }
  }
  // なにもなければ空を戻す
  return '';
}

// ラインの送信
function send_line(user_token, message){
  let token = PropertiesService.getScriptProperties().getProperty("line_token");
  let url = 'https://api.line.me/v2/bot/message/multicast';
  let payload = {
    'to':[user_token],
    'messages': [{
        'type': 'text',
        'text': message
      }]
  };
  let options = {
    'payload' : JSON.stringify(payload),
    'myamethod'  : 'POST',
    'headers' : {"Authorization" : "Bearer " + token},
    'contentType' : 'application/json'
  };
  UrlFetchApp.fetch(url, options);
}

// シフト管理用定期更新（15分周期）
function update_schedule(){
  const url = PropertiesService.getScriptProperties().getProperty('update_schedule_url');
  try{
    let res = UrlFetchApp.fetch(url).getContentText();
    let res_json = JSON.parse(res);
    if(res_json!=[]){
      for (let i = 0; i < res_json.length; i++) {
        try{
          var task = res_json[i];
          let line_id = get_line_id(Number(task.user_id));
          if (line_id != ''){
            send_line(line_id, task.message)
          }
        } catch (error){
          continue
        }
      }
    }
    Logger.log(res_json);
  } catch (error) {
    Logger.log(error)
    return 'サーバーエラー'
  }
}

// POST　管理者へメッセージを送信する
function doPost(e){
  var jsonString = e.postData.getDataAsString();
  var data = JSON.parse(jsonString);
  let pass_true = PropertiesService.getScriptProperties().getProperty("post_pass");
  // IDとパスワードがあっていたら管理者にメッセージを送信
  if(data.user_id == 0 && data.pass == pass_true){
    let line_token = get_line_id(0);
    if (line_token != ''){
      send_line(line_token, data.message);
    }
  }
  return ContentService.createTextOutput(JSON.stringify(e));
}

// 統計管理用定期更新（毎月1日更新）
function update_work_status(){
  // 更新するのは先月の分のため機能のデータを取得
  var today = new Date();
  var yesterday = new Date(today.getFullYear(), today.getMonth(), today.getDate()-1);
  var year_string = Utilities.formatDate(yesterday, "JST", "yyyy");
  var month_string = Utilities.formatDate(yesterday, "JST", "MM");
  // 更新
  post_work_status(year_string,month_string);
}

// 年月を指定してテーブルを更新
function post_work_status(year_string,month_string){
  var data = {
    "key_year":year_string,
    "key_month":month_string
  };
  var headers = {
    "Content-Type": "application/json"
  };
  var options = {
    "headers": headers,
    "Content-Type": "application/json",
    "method": "post",
    "payload": JSON.stringify(data)
  };

  const url = PropertiesService.getScriptProperties().getProperty('update_work_status_url');
  let res = UrlFetchApp.fetch(url, options).getContentText();
  let res_json = JSON.parse(res);
  return res_json;
}

// デバック用
function test(){
  Logger.log(post_work_status('2023','02'));
}


