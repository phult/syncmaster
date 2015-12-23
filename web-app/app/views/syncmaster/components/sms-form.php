<form action="index.html" id="formAddList" method="get">
    <div class="formLeft">
        <label><b>{{sms.type.indexOf('in') > -1 ? 'From' : 'To'}}</b></label>
        <a class="label" 
           ng-click="sms.contact = null"
           ng-show="sms.contact != null">{{sms.contact.name + " (" + formatPhone(sms.contact.phone) + ")"}}</a>
        <input class="item-nameAdd" name="LinkSong" type="text" autocomplete="off"
               ng-show="sms.contact == null"
               ng-disabled="sms.id != null"
               ng-model="sms.phone"
               ng-change="contactSugestions = suggestContacts(sms.phone)">
        <ul class="droplist" ng-show="contactSugestions != null && contactSugestions.length > 0">
            <li ng-repeat="contact in contactSugestions"
                ng-click="selectContactSuggestion(contact, contactSugestions);">{{contact.name + " (" + formatPhone(contact.phone) + ")"}}</li>            
        </ul>
    </div>
    <div class="clear"></div>
    <div class="formLeft">
        <label><b>Message</b></label>
        <textarea rows="4"
                  ng-disabled="sms.id != null"
                  ng-model="sms.data"></textarea>
    </div>
    <div class="formLeft" style="font-style: italic;color: red">        
        <p ng-repeat="error in smsForm.errors">{{error}}</p>
    </div>
    <div class="clear" align="center" style="padding:10px">
        <input type="button" class="button" value="Send Now"
               ng-show="sms.id == null"
               ng-click="sendSMS();">
        <input type="button" class="button" value="Reply SMS"
               ng-show="sms.id != null"
               ng-click="showSMSForm(null, sms.contact);">
    </div>
</form>