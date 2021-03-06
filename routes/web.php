<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/settings-accounts-list','AccountController@AccountsList')->name('AccountsListGM');
Route::get('/get-all-managers','AccountController@getallManagers');
Route::get('/sort-by-role-and-search','AccountController@getSelectedRoleAndSearch');
Route::get('/fetchDataofSelectedUser/{id}','AccountController@fetchDataofSelectedUser')->name('fetchDataUser');
Route::put('/update-user-data/{id}','AccountController@updateUser');
Route::delete('/deleteAccount/{id}','AccountController@deleteAccount');
Route::get('/show-my-history','AccountHistoryController@ShowMyHistoryPage')->name('ViewMyHistory');
Route::get('/search-my-mirs-history','AccountHistoryController@MyMIRSHistoryandSearch');
Route::get('/search-my-mct-history','AccountHistoryController@MyMCTHistoryandSearch');
Route::get('/search-my-mrt-history','AccountHistoryController@MyMRTHistoryandSearch');
Route::get('/search-my-mr-history','AccountHistoryController@MyMRHistoryandSearch');
Route::get('/search-my-rv-history','AccountHistoryController@MyRVHistoryandSearch');
Route::get('/search-my-rr-history','AccountHistoryController@MyRRHistoryandSearch');
Route::get('/login','AccountController@loginpage')->name('login')->middleware('guest');
Route::post('/logout','AccountController@logoutAccount')->name('Logging.out');
Route::post('/login-submit','AccountController@loginSubmit')->name('login-submit');
Route::get('/','ItemsController@index')->name('WelcomePage');
Route::get('/search-by-description-and-recently-inits','ItemsController@SearchDescriptionAndRecentAdded')->middleware('IsAdmin');
Route::get('/fetchData-of-item-to-be-edited/{id}','ItemsController@FetchItemToEdit');
Route::put('/update-changes-item/{id}','ItemsController@UpdateItem');
Route::get('/search-item-data','ItemsController@FetchAndsearchItemData')->name('search.code');
Route::get('/search-item-autocomplete','ItemsController@autocompleteSearch');
Route::get('/MIRS-add','MIRSController@MIRScreate')->name('mirs.add')->middleware('MustNotGMorManager');
Route::get('/fetchSessionMIRS','MIRSController@fetchSessionMIRS');
Route::post('/sessionMIRSitem','MIRSController@addingSessionItem')->name('selecting.item');
Route::delete('/removeSessions/{id}','MIRSController@deletePartSession')->name('delete.session');
Route::post('/mirs-storedata','MIRSController@StoringMIRS')->name('mirs.store');
Route::get('/MIRS.pdf/{id}','PDFController@mirspdf')->name('mirs-download');
Route::get('/MCTSUMMARY.pdf','PDFController@MCTsummaryprint')->name('mct-summary-print')->middleware('IsWarehouseAndAdmin');
Route::get('/previewFullMIRS/{id}','MIRSController@fullMIRSview')->name('full-mirs');
Route::get('/fetchpreview-full-mirs-/{id}','MIRSController@fetchFullMIRSData');
Route::put('/deniedmirs/{id}','MIRSController@DeclineMIRS')->name('DeniedMIRS');
Route::get('/findmirs-and-fetch','MIRSController@searchMIRSNoAndFetch')->name('finding.mirs');
Route::get('/mirs-index-page','MIRSController@MIRSIndexPage')->name('MIRSgridview');
Route::put('/mirs-update/{mirsNo}','MIRSController@QuickUpdate');
Route::post('/MCTstore','MCTController@StoreMCT')->name('Storing.MCT');
Route::get('/preview-mct-page-only/{id}','MCTController@previewMCTPage')->name('MCTpageOnly');
Route::get('MCTpreview/{id}','MCTController@previewMCT')->name('previewMCT');
Route::get('/mct-index-page','MCTController@MCTindexPage')->name('indexMCT');
Route::get('/mct-index-fetchdata','MCTController@fetchSearchIndexMCTlist');
Route::get('/mct-create-fetch-session-data','MCTController@displayMCTSessionStored');
Route::get('/create-mct/{MIRSNo}','MCTController@CreateMCT')->name('toRecordingMCT.Page');
Route::get('/fetch-mct-validator/{MIRSNo}','MCTController@FetchMCTvalidator');
Route::post('/mct-session-saving','MCTController@MCTSessionSaving')->name('sessionSaveMCT');
Route::delete('/delete-session-mct/{id}','MCTController@deleteASession')->name('delete.a.session.mct');
Route::get('/MCT.pdf','PDFController@mctpdf')->name('print-mct');
Route::get('/MCTofMIRS/{id}','MCTController@MCTofMIRS')->name('MCTofMIRS');
Route::put('/update-mct/{id}','MCTController@updateMCT')->name('MCTUpdate');
Route::get('/getItemsMCTvalidator/{id}','MCTController@getItemsMCTvalidator')->name('getValidatorofMCT');
Route::put('/updateMRTQty/{id}','MRTController@updateQuantityMRT')->name('updateQtymrt');
Route::get('/MRT-create/{id}','MRTController@CreateMRT')->name('create.mrt');
Route::get('/MRT-create-fetch-mct-of-mrt/{mctno}','MRTController@CreateMRTFetchMCTdata');
Route::post('/MRT-store/{id}','MRTController@StoreMRT')->name('storing.mrt')->middleware('IfAlreadyHaveMRT');
Route::post('/MRT-session','MRTController@addToSession');
Route::get('/display-session-mrt','MRTController@DisplaySessionMRT');
Route::delete('/MRT-delete/{id}','MRTController@deletePartSession')->name('mrtSession.deleting');
Route::get('summary-mrt','MRTController@summaryMRT')->name('summary.mrt')->middleware('IsWarehouseAndAdmin');
Route::get('mrt-find-date','MRTController@MRTSearchdate')->name('mrt.summary.find')->middleware('IsWarehouseAndAdmin');
Route::get('/MRTSUMMARY.pdf','PDFController@mrtpdf')->name('mrt-summary-print')->middleware('IsWarehouseAndAdmin');
Route::get('/mrt-preview-page/{id}','MRTController@toMRTpreviewPage')->name('MRTpreviewPage');
Route::get('/mrt-viewer/{id}','MRTController@mrtviewing')->name('mrt-viewer');
Route::put('/signatureMRT/{id}','MRTController@signatureMRT');
Route::put('/declineMRT/{id}','MRTController@declineMRT');
Route::get('/mrt-index-page','MRTController@MRTindexPage')->name('MRTindexPageonly');
Route::get('/mrt-index-fetch-search','MRTController@MRTindexSearch');
Route::get('/my-mrt-signature-request','MRTController@myMRTSignatureRequest')->name('MyMRTSignatureRequest');
Route::get('/my-mrt-signature-request-fetchdata','MRTController@myMRTSignatureFetchData');
Route::get('/print-mrt.pdf/{id}','PDFController@MRTPrinting');
Route::get('mct-summary','MCTController@summaryMCT')->name('mct-summary')->middleware('IsWarehouseAndAdmin');
Route::put('/MIRS-Signature/{id}','MIRSController@MIRSSignature')->name('MIRSSign');
Route::get('/item-search','ItemsController@ItemMasterSearch')->name('ItemsearchDescription');
Route::put('/Signature-for-mct/{id}','MCTController@SignatureMCT')->name('MCTsignature');
Route::put('/decline-mct/{id}','MCTController@declineMCT')->name('MCTDecline');
Route::get('/mirs-signature-list','MIRSController@mirsRequestcheck')->name('checkmyMIRSrequest');
Route::get('/mct-signature-request','MCTController@mctRequestcheck')->name('checkmyMCTrequest');
Route::put('/cancel-request-approval/{id}','MIRSController@CancelApproveMIRSinBehalf')->name('cancel-request-to-asigned');
Route::put('/confirm-manager-toreplace-gm-signature/{id}','MIRSController@AcceptApprovalRequest')->name('letManagerApprove');
Route::get('/fetch-all-managers','MIRSController@fetchAllManager');
Route::post('/send-request-manager-replacer/{id}','MIRSController@SendRequestManagerReplacer');
Route::delete('/cancel-request-manager-replacer/{id}','MIRSController@CancelRequestManagerReplacer');
Route::put('/signature-replacer-accepted/{id}','MIRSController@SignatureManagerReplacer');
Route::get('/mct-summary-search','MCTController@searchMCTsummary')->name('mct-search-date')->middleware('IsWarehouseAndAdmin');
Route::delete('/delete-session-with-po/{id}','RRController@deleteSessionStored')->name('RRDeleteSession');
Route::delete('/delete-session-no-po/{id}','RRController@deleteSessionNoPo');
Route::put('/update-rr-file/{RRNo}','RRController@updateRR');
Route::get('RRsearchitembyCode','RRController@searchbyItemMasterCode')->name('RRSearchItemCode');
Route::post('/rr-storing-session-no-po','RRController@StoreSessionRRNoPO')->name('storeSessionRR');
Route::post('/rr-storing-session-with-po','RRController@StoreSessionRRWithPO');
Route::get('/show-rr-session-data','RRController@showSessionRRData')->name('showing.currentsession.rr');
Route::get('/show-rr-session-data-no-po','RRController@showSessionRRDataNoPO');
Route::post('/save-rr-no-po-to-db','RRController@StoreRRtoTableNoPO')->name('SaveRRNoPO');
Route::post('/save-rr-with-po-to-db','RRController@StoreRRtoTableWithPO')->name('SaveRRWithPO');
Route::get('/displayRRcreateSession','RRController@displayRRcurrentSession');
Route::get('/RR-index','RRController@RRindex')->name('RRindexview');
Route::get('/RR-index-fetch-and-search','RRController@RRindexSearchAndFetch');
Route::get('/RR-fullpreview/{id}','RRController@previewRR')->name('RRfullpreview');
Route::get('/RR-fullpreview-fetch-data/{id}','RRController@previewRRfetchdata');
Route::put('/RR-signature/{id}','RRController@signatureRR')->name('RRsigning');
Route::get('/checkout-rr-request','RRController@RRsignatureRequest')->name('checkmyRRrequest');
Route::get('/RR.pdf/{id}','PDFController@rrdownload')->name('RR-printing');
Route::put('/decline-this-RR/{id}','RRController@declineRR')->name('RRdecline');
Route::get('/create-rr-wo-po/{id}','RRController@CreateRRNoPO')->name('CreatingRR.without.po')->middleware('IfAlreadyHavePO');
Route::get('/create-rr-w-po/{id}','RRController@CreateRRWithPO')->name('CreateingRR.with.po');
Route::get('/rr-of-rv-list/{id}','RRController@RRofRVlist')->name('showRR-ofRV');
Route::get('/rr-of-po-list/{id}','RRController@RRofPOlist');
Route::put('/rv-update/{RVNo}','RVController@updateRV');
Route::get('RV-create','RVController@RVcreate')->name('Creating.RV')->middleware('MustNotGMorManager');
Route::get('/fetch-rv-session','RVController@fetchSessionRV');
Route::post('/session-saving-rv','RVController@SaveSession')->name('SavingSessionRV');
Route::delete('/DeleteSession/{id}','RVController@DeleteSession')->name('DeletingSessionRV');
Route::post('/SavetoDBRV','RVController@savingToTable')->name('SavingRVtoDB');
Route::get('RVindex','RVController@RVindexView')->name('RVindexView');
Route::get('indexRVVUE','RVController@RVIndexSearch');
Route::get('RVfullview/{id}','RVController@RVfullPreview')->name('RVfullpreviewing');
Route::get('rv-full-preview-fetch/{id}','RVController@RVfullpreviewFetchData');
Route::put('/RVsignature/{id}','RVController@Signature')->name('SignatureThisRV');
Route::get('/myRVrequest','RVController@RVrequest')->name('MyRVrequestlist');
Route::put('/declineRV/{id}','RVController@declineRV')->name('DeclineRV');
Route::get('search-rv','RVController@searchRV')->name('SearchingRV');
Route::get('/search-rv-forstock','RVController@searchRVforStock')->name('SearchDescriptionRVstock')->middleware('IsWarehouse');
Route::post('/addtoStockSession','RVController@addtoStockSession')->name('AddRVforStockSession')->middleware('IsWarehouse');
Route::get('/get-low-qty-items','RVController@getBelowminimumItems');
Route::get('/RV.pdf/{id}','PDFController@RVdownload')->name('downloadRVprint');
Route::get('/fetchAllManagerRV','RVController@fetchAllManagerRV');
Route::put('/rv-signature-in-behalf-cancel/{id}','RVController@cancelSignatureApproveInBehalf')->name('cancel-rv-approve-inbehalf');
Route::put('/rv-approve-behalf-accept/{id}','RVController@AcceptSignatureBehalf')->name('ConfirmSignatureinBehalfRV');
Route::put('/send-request-to-manager-replacer/{id}','RVController@sendRequestManagerReplacer');
Route::put('/cancelrequestsentReplacer/{id}','RVController@cancelrequestsentReplacer');
Route::put('/AcceptManagerReplacer/{id}','RVController@AcceptManagerReplacer');
Route::put('/save-budget-officer-pending-remarks/{id}','RVController@savePendingRemark');
Route::get('/show-budget-officer-pending-remarks/{id}','RVController@showBORemarks');
Route::put('/budget-officer-pending-remarks/{id}','RVController@PedingRemarkRemove');
Route::get('/CanvassCreate/{id}','CanvassController@TocanvassPage')->name('TocanvassPage')->middleware('RRWithoutPO');
Route::get('/canvass-suppliers/{id}','CanvassController@getSupplierRecords');
Route::delete('/deleteCanvassRecord/{id}','CanvassController@deleteCanvassRecord');
Route::post('supplier-save-canvass','CanvassController@saveCanvass')->name('SupplierCanvasSaving');
Route::post('generate-po','POController@GeneratePOfromCanvass')->name('generatePO')->middleware('IsWarehouse');
Route::get('/search-supplier/{id}','CanvassController@searchSupplier')->name('search.supplier');
Route::put('/update-canvass/{id}','CanvassController@canvassUpdate');
Route::get('po-list-view-of-rv/{id}','POController@POListView')->name('POListView');
Route::get('/po-full-preview/{id}','POController@POFullpreview')->name('POFullView');
Route::get('/po-full-preview-fetch/{id}','POController@POFullPreviewFetch');
Route::put('/gm-signature-po/{id}','POController@GMSignaturePO')->name('GMSignatureOrder');
Route::put('/gm-decline-po/{id}','POController@GMDeclined')->name('GMDeclining');
Route::get('/my-PO-request','POController@MyPOrequestlist')->name('viewPOrequest');
Route::put('cancel-authorize-inbehalf/{id}','POController@CancelAuthorizeInBehalf')->name('AuthorizeInBehalfCancel');
Route::put('/authorize-in-behalf-confirmed/{id}','POController@AuthorizeInBehalfconfirmed')->name('authorizeinbehalf-confirm');
Route::get('/waiting-to-be-purchased-rv','RVController@UnpurchaseList')->name('pending-purchase-rv');
Route::put('/declined-Authorize-inbehalf/{id}','POController@RefuseAuthorizeInBehalf')->name('autorizeInBehalf.denybyadmin');
Route::put('/update-budget/{id}','RVController@updateBudgetAvailable')->name('Update.budget');
Route::get('/PO.pdf/{id}','PDFController@POdownload')->name('downloadPO');
Route::get('/po-index-page','POController@indexPoPage')->name('POIndexPage');
Route::get('/po-index-fetch-and-search','POController@fetchAndSearchPOindex');
Route::put('/mr-update/{MRNo}','MRController@updateMR');
Route::post('save-mr','MRController@SaveMR')->name('saveMR');
Route::get('/view-list-MR-of-RR/{id}','MRController@MRofRRlist')->name('ViewMR.ofRR');
Route::get('/full-preview-MR/{id}','MRController@previewFullMR')->name('fullMR');
Route::get('/full-preview-MR-Fetch/{id}','MRController@previewFullMRFetchData');
Route::get('/MR.pdf/{id}','PDFController@MRprinting')->name('printMR');
Route::get('/create-mr/{id}','MRController@createMR')->name('create-mr');
Route::post('addSession-MR','MRController@addSessionForMR');
Route::get('displaySessionMR','MRController@displayMRSessions');
Route::delete('deletemrSession/{id}','MRController@removingSession');
Route::put('/signature-MR/{id}','MRController@SignatureMR')->name('SignatureMR');
Route::put('/Decline-MR/{id}','MRController@DeclineMR')->name('DeclineMR');
Route::get('/my-mr-request','MRController@myMRrequest')->name('myMR.signature.Request')->middleware('auth');
Route::put('mr-approve-inbehalf/{id}','MRController@approveinBehalfMRsent')->name('ApproveMR.inbehalf');
Route::put('/mr-approve-inbehalf-refused/{id}','MRController@refuseMRApproveInBehalf')->name('ApproveinBehalfCancel');
Route::put('/confirmApproveinBehalf/{id}','MRController@confirmApproveinBehalf')->name('confirmApproveInBehalf');
Route::get('/mr-index-page','MRController@MRindexPage')->name('MRIndexPage');
Route::get('/mr-index-fetch-and-search','MRController@MRindexFetchAndSearch');
Route::get('/create-non-existing-item-in-warehouse','ItemsController@addNonexistinwarehouseItem')->name('create-none-exist-item');
Route::post('/save-New-Item-warehouse','ItemsController@SaveNewItem')->name('save-none-exist-warehouse')->middleware('IsAdmin');
Route::post('/add-new-unit-to-dropdown','UnitController@SaveUnit')->name('save-unit');
Route::get('/get-all-units','UnitController@fetchUnits');
Route::delete('/delete-unit/{id}','UnitController@DeleteUnit');
Route::get('/manager-take-placer-setting','AccountController@toManagerTakePlacePage');
Route::get('/get-all-active-manager','AccountController@getActiveManager');
Route::put('/update-manager-to-take-place','AccountController@UpdateManagerTakePlace');
Route::get('/get-current-assigned-bygm','AccountController@getCurrentAssigned');
Route::get('/mirs-notify','MIRSController@MIRSNotification');
Route::get('/mirs-approved-new-notify','MIRSController@newlyApprovedMIRSCount');
Route::get('/mct-new-created-notify','MCTController@MCTRequestSignatureCount');
Route::get('/mrt-new-created-notify','MRTController@MRTSignatureRequestCount');
Route::get('/rv-new-created-notify','RVController@RVRequestCount');
Route::get('/rv-waiting-for-all-items-receiving-report','RVController@RefreshRVWaitingForRRCount');
Route::get('/rv-new-created-rr-notify','RRController@refreshRRSignatureCount');
Route::get('/rv-new-created-mr-notify','MRController@MyMRrequestCount');
Route::get('/po-count-notification','POController@MyPORequestCount');
Route::post('/saving-account-manager','AccountController@SaveManagerAcc');
Route::post('/save-account-user','AccountController@SaveNewUser');
Route::get('/my-own-account-settings-page','MyAccountSettings@MyAccountSettingsPage');
Route::get('/my-own-account-fetch-data','MyAccountSettings@FetchMyData');
Route::put('/update-account-contact', 'MyAccountSettings@updateContact');
Route::put('/update-account-username','MyAccountSettings@updateUserName');
Route::put('/update-account-password','MyAccountSettings@changeMyPassword');
Route::get('/show-data','dashBoardController@show');

Route::put('/rollback-mct-history/{mctNo}/{mirsNo}','MCTController@RollBack');
Route::put('/undo-rollback-mct-history/{mctNo}/{mirsNo}','MCTController@UndoRollBack');

Route::put('/rollback-mrt-history/{mrtNo}','MRTController@RollBack');
Route::put('/undo-rollback-mrt-history/{mrtNo}','MRTController@UndoRollBack');

Route::put('/rollback-this-rr/{rrNo}','RRController@RollBack');
Route::put('/undo-rollback-this-rr/{rrNo}','RRController@UndoRollBack');

Route::get('/line-chart-data','dashBoardController@lineChart');
Route::get('/bar-chart-data','dashBoardController@barChart');

// notification global
Route::get('/fetch-global-notifications','globalNotificationController@fetchNotifications');
Route::get('/notif-global-count','globalNotificationController@countInfoNotification');
Route::get('/mark-seen/{id}','globalNotificationController@markSeen');
Route::get('/mark-all-seen','globalNotificationController@markAllSeen');

// damage item recording
Route::post('/damage-item-store/{itemcode}','DamagedItemController@store');
Route::delete('/damage-item-delete/{id}/{itemcode}','DamagedItemController@delete');

Route::get('/all-users-status','dashBoardController@UsersStatusCheck');

//recent files
Route::get('/recent-files-get','dashBoardController@getRecentTransactions');
Route::get('/transactions-count','dashBoardController@countUserTransactions');

// excel
Route::get('/export-excel-mct-summary','excelController@exportExelMCT');
Route::get('/export-excel-mrt-summary','excelController@exportExelMRT');
