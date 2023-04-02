// レンタルサーバーからIDチェック
function id_check(user_id, user_name){
  var data = {
    "user_id":user_id,
    "user_name":user_name
  };
  var options = {
    "Content-Type": "application/json",
    "method": "post",
    "payload": JSON.stringify(data)
  };
  const url = PropertiesService.getScriptProperties().getProperty("check_id_url");
  let res = UrlFetchApp.fetch(url, options).getContentText();
  let res_json = JSON.parse(res);
  return [res_json["success"], res_json["message"]]
}

// スプレットシートにユーザーを追加
function add_user(line_id,message){
  // メッセージからデータ抽出
  const [_, user_id, user_name] = message.split(/\nID:|\nName:/);
  // スプレットシートの取得
  const db_id = PropertiesService.getScriptProperties().getProperty("database_id");
  let table_member = SpreadsheetApp.openById(db_id).getSheetByName('table_member');
  let last_row = table_member.getLastRow();
  // LINE_IDの被りチェック
  let line_id_list = table_member.getRange(2,1,last_row,1).getValues().flat();
  if(line_id_list.indexOf(line_id)!=-1.0){
    return 'Error:このLINEアカウントはすでに登録されています';
  }
  // User_IDの被りチェック
  let user_id_list = table_member.getRange(2,2,last_row,2).getValues().flat();
  if(user_id_list.indexOf(user_id)!=-1.0){
    // 被っていたら失敗にする
    return 'Error:このIDはすでに登録されています';
  }
  // 本人確認(IDチェック)
  let[success, error_message] = id_check(user_id, user_name);
  if(!success){
    // 本人確認ができなかったら失敗
    return 'Error:本人確認が失敗しました\n'+error_message;
  }
  // 被りが無ければ追加する
  let add_array = [line_id,user_id];
  table_member.appendRow(add_array);
  return '登録完了';
}

// スプレットシートに管理者を登録
function add_admin(line_id){
  // 登録するuser_idは0
  const user_id = 0;
  // スプレットシートの取得
  const db_id = PropertiesService.getScriptProperties().getProperty("database_id");
  let table_member = SpreadsheetApp.openById(db_id).getSheetByName('table_member');
  let last_row = table_member.getLastRow();
  // すでに登録されているかチェック
  let user_id_list = table_member.getRange(2,2,last_row,2).getValues().flat();
  let admin_row = user_id_list.indexOf(user_id);
  if(admin_row !=-1.0){
    // 登録されていたら上書き
    table_member.getRange(admin_row+1,1).setValue(line_id);
    return '登録を上書きしました';
  }else{
    // 未登録なら追加
    let add_array = [line_id,user_id];
    table_member.appendRow(add_array);
    return '登録完了';
  }
}

// ユーザーIDの取得
function get_user_id(line_id){
  // スプレットシートの取得
  const db_id = PropertiesService.getScriptProperties().getProperty("database_id");
  let table_member = SpreadsheetApp.openById(db_id).getSheetByName('table_member');
  let last_row = table_member.getLastRow();
  // ユーザーIDの取得
  let user_id_list = table_member.getRange(1,1,last_row,1).getValues().flat();
  let user_row = user_id_list.indexOf(line_id);
  // ユーザーIDが見つからない時
  if (user_row == -1){
    return -1
  }
  let user_id = table_member.getRange(user_row+1,2).getValue();
  // 管理者以外ならユーザーIDを確定
  if (user_id != 0){
    return user_id
  }else{
    // 管理者と被っていたら除外して取得しなおす
    user_id_list.splice(user_row,1);
    user_row = user_id_list.indexOf(line_id);
    if (user_row == -1){
      return -1
    }
    user_id = table_member.getRange(user_row+2,2).getValue();
    return user_id
  }
}

// 出勤、退勤の操作をサーバーに送信
function action_post(line_id, post_url){
  let user_id = get_user_id(line_id);
  if(user_id == -1){
    return 'ユーザーを登録してください'
  }
  // 送信データの作成
  var data = {
    "user_id":user_id,
  };
  var options = {
    "Content-Type": "application/json",
    "method": "post",
    "payload": JSON.stringify(data)
  };
  const url = PropertiesService.getScriptProperties().getProperty(post_url);
  // 送信
  try{
    let res = UrlFetchApp.fetch(url, options).getContentText();
    let res_json = JSON.parse(res);
    // 登録処理ができたか判定
    if (res_json['success']){
      return res_json['message'];
    }else{
      return '処理エラー'
    }
  } catch (error) {
    return 'サーバーエラー'
  }
}

// 欠勤の操作をサーバーに送信
function absenteeism_post(line_id, date){
  let user_id = get_user_id(line_id);
  if(user_id == -1){
    return 'ユーザーを登録してください'
  }
  // 送信データの作成
  var data = {
    "user_id":user_id,
    "date":date
  };
  var options = {
    "Content-Type": "application/json",
    "method": "post",
    "payload": JSON.stringify(data)
  };
  const url = PropertiesService.getScriptProperties().getProperty('absenteeism_url');
  // 送信
  try{
    let res = UrlFetchApp.fetch(url, options).getContentText();
    let res_json = JSON.parse(res);
    // 登録処理ができたか判定
    if (res_json['success']){
      return res_json['message'];
    }else{
      return '処理エラー'
    }
  } catch (error) {
    return 'サーバーエラー'
  }
}

// 返信メッセージの作成
function create_response_message(line_id, received_message){
  // 返信するテキストのデフォルト
  let send_message = "サポートされていません"
  // 出勤
  if (received_message=="#出勤"){
    send_message = action_post(line_id,'check_in_url');
    return send_message
  }
  // 退勤
  if (received_message=='#退勤'){
    send_message = action_post(line_id,'check_out_url');
    return send_message
  }
  // 欠勤
  let absenteeism_format = new RegExp(/^#欠勤\n/);
  if (absenteeism_format.test(received_message)){
    let date = received_message.split('\n')[1];
    Logger.log(date);
    send_message = absenteeism_post(line_id, date);
    return send_message
  }
  // 追加書式なら追加を行う
  let add_format = new RegExp(/^#個人登録\nID:\d+\nName:.+$/);
  if(add_format.test(received_message)){
    send_message = add_user(line_id, received_message);
    return send_message
  }
  // 管理者の設定ならLINEを登録する
  if (received_message=="#勤務管理者登録\nID:admin_fwm\nPASS:1234567890"){
    send_message = add_admin(line_id);
    return send_message
  }
  return send_message
}

function doPost(e) {
  // ラインからの情報を取得
  let token = PropertiesService.getScriptProperties().getProperty("line_token");
  let eventData = JSON.parse(e.postData.contents).events[0];
  let replyToken = eventData.replyToken;
  // 返信メッセージの作成
  let send_message = create_response_message(eventData.source.userId, eventData.message.text);
  // 返信
  let url = 'https://api.line.me/v2/bot/message/reply';
  let payload = {
    'replyToken': replyToken,
    'messages': [{
        'type': 'text',
        'text': send_message
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


function test(){
  // const db_id = PropertiesService.getScriptProperties().getProperty("database_id");
  // let table_member = SpreadsheetApp.openById(db_id).getSheetByName('table_member');
  // let last_row = table_member.getLastRow();
  // let message = table_member.getRange(last_row,2).getValue();
  // Logger.log(message);
  // let add_format = new RegExp(/^個人登録\nID:\d+\nName:.+$/);
  // Logger.log(add_format.test(message));
  // let line_id_list = table_member.getRange(1,1,last_row,1).getValues().flat();
  // Logger.log(line_id_list.indexOf('U42367044671187e8d6e551702001d086'));
  let id = 'test2';
  let mes = '個人登録\nID:1\nName:test1';
  mes = '#欠勤\n4月2日'
  // Logger.log(id_check('1','test1'))
  // Logger.log(add_user(id,mes))
  Logger.log(create_response_message(id, mes));
}
