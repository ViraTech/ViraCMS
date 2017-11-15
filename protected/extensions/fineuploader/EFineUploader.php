<?php
/**
 * Wrapper for Fine Uploader Plugin (http://fineuploader.com)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class EFineUploader extends CWidget
{
  // defaults
  const DEFAULT_TAG = 'div';
  const DEFAULT_DEBUG = false;
  const DEFAULT_MULTIPLE = true;
  const DEFAULT_MAX_CONNECTIONS = 3;
  const DEFAULT_DISABLE_CANCEL_FOR_FORM_UPLOADS = false;
  const DEFAULT_AUTO_UPLOAD = true;
  const DEFAULT_ENDPOINT = '/upload';
  const DEFAULT_FORCE_MULTIPART = false;
  const DEFAULT_INPUT_NAME = 'qqfile';
  const DEFAULT_SIZE_LIMIT = 0;
  const DEFAULT_MIN_SIZE_LIMIT = 0;
  const DEFAULT_STOP_ON_FIRST_INVALID_FILE = true;
  const DEFAULT_ENABLE_AUTO = false;
  const DEFAULT_MAX_AUTO_ATTEMPTS = 3;
  const DEFAULT_AUTO_ATTEMPT_DELAY = 5;
  const DEFAULT_PREVENT_RETRY_RESPONSE_PROPERTY = 'preventRetry';
  const DEFAULT_HIDE_DROPZONES = true;
  const DEFAULT_DISABLE_DEFAULT_DROPZONES = false;
  const DEFAULT_UPLOAD_BUTTON = 'Upload a file';
  const DEFAULT_RETRY_BUTTON = 'Retry';
  const DEFAULT_CANCEL_BUTTON = 'Cancel';
  const DEFAULT_FAIL_UPLOAD = 'Upload failed';
  const DEFAULT_DRAG_ZONE = 'Drop files here to upload';
  const DEFAULT_FORMAT_PROGRESS = '{percent}% of {total_size}';
  const DEFAULT_WAITING_FOR_RESPONSE = 'Processing...';
  const DEFAULT_MODE = 'default';
  const DEFAULT_MAX_CHARS = 50;
  const DEFAULT_RESPONSE_PROPERTY = 'error';
  const DEFAULT_ENABLE_TOOLTIP = true;
  const DEFAULT_SHOW_AUTO_RETRY_NOTE = true;
  const DEFAULT_AUTO_RETRY_NOTE = 'Retrying {retryNum}/{maxAuto}...';
  const DEFAULT_SHOW_BUTTON = false;
  const DEFAULT_LOADING_CLASS = 'qq-upload-progress';
  const DEFAULT_BUTTON_CLASS = 'qq-upload-button';
  const DEFAULT_DROP_CLASS = 'qq-upload-drop-area';
  const DEFAULT_DROP_ACTIVE_CLASS = 'qq-upload-drop-area-active';
  const DEFAULT_DROP_DISABLED_CLASS = 'qq-upload-drop-area-disabled';
  const DEFAULT_LIST_CLASS = 'qq-upload-list';
  const DEFAULT_PROGRESS_BAR_CLASS = 'qq-progress-bar';
  const DEFAULT_FILE_CLASS = 'qq-upload-file';
  const DEFAULT_SPINNER_CLASS = 'qq-upload-spinner';
  const DEFAULT_FINISHED_CLASS = 'qq-upload-finished';
  const DEFAULT_RETRYING_CLASS = 'qq-upload-retrying';
  const DEFAULT_RETRYABLE_CLASS = 'qq-upload-retryable';
  const DEFAULT_SIZE_CLASS = 'qq-upload-size';
  const DEFAULT_CANCEL_CLASS = 'qq-upload-cancel';
  const DEFAULT_RETRY_CLASS = 'qq-upload-retry';
  const DEFAULT_STATUS_TEXT_CLASS = 'qq-upload-status-text';
  const DEFAULT_SUCCESS_CLASS = 'qq-upload-success';
  const DEFAULT_FAIL_CLASS = 'qq-upload-fail';
  const DEFAULT_SUCCESS_ICON_CLASS = null;
  const DEFAULT_FAIL_ICON_CLASS = null;

  /**
   * @var string URL of published assets
   */
  private $assetsUrl;

  /**
   * @var boolean Automatic element rendering
   */
  public $renderElement = true;

  /**
   * @var boolean Only init widget but do not run it (need to split init and run parts)
   */
  public $onlyInit = false;

  /**
   * @var boolean Only run widget but do not init it (need to split run and init parts)
   */
  public $onlyRun = false;

  /**
   * @var boolean Output script block as <script> when running instead of use CClientScript
   */
  public $scriptOutput = false;

  /**
   * @var string Name of input element
   */
  public $name;

  /**
   * @var array Top container HTML options
   */
  public $htmlOptions = array();

  /**
   * @var string Tag name for input area rendering
   */
  public $tag = self::DEFAULT_TAG;

  /**
   * @var boolean Debug mode
   */
  public $debug = self::DEFAULT_DEBUG;

  /**
   * @var string Javascript expression returning HTML-element of default drop zone (if supported by browser) and files list
   */
  public $element;

  /**
   * @var string Javascript expression returning HTML-element of files list
   */
  public $listElement;

  /**
   * @var string Specify an element to use as the "select files" button. Note that this may NOT be a <button>, otherwise it will not work in Internet Explorer
   */
  public $button;

  /**
   * @var boolean Allow upload multiple files
   */
  public $multiple = self::DEFAULT_MULTIPLE;

  /**
   * @var integer Maximum available concurrent connections
   */
  public $maxConnections = self::DEFAULT_MAX_CONNECTIONS;

  /**
   * @var boolean Disables cancel button when file being upload
   */
  public $disableCancelForFormUploads = self::DEFAULT_DISABLE_CANCEL_FOR_FORM_UPLOADS;

  /**
   * @var boolean Automatic uploading when file being selected
   */
  public $autoUpload = self::DEFAULT_AUTO_UPLOAD;

  /**
   * @var string Request options. Server endpoint to AJAX uploader
   */
  public $endpoint = self::DEFAULT_ENDPOINT;

  /**
   * @var array Request options. Request params sent with every connect
   */
  public $params = array();

  /**
   * @var array Request options. Custom headers sent with every connect
   */
  public $customHeaders = array();

  /**
   * @var boolean Request options. Force multipart for any requests
   */
  public $forceMultipart = self::DEFAULT_FORCE_MULTIPART;

  /**
   * @var string Request options. Defines uploaded files keys named as uploaded to endpoint
   */
  public $inputName = self::DEFAULT_INPUT_NAME;

  /**
   * @var array Validation options. Allowed file extensions to upload
   */
  public $allowedExtensions = array();

  /**
   * @var string Validation options. Comma separated extensions to be applied on browser' choose dialog
   */
  public $acceptFiles;

  /**
   * @var integer Validation options. Maximum allowed file size to upload. Zero means no limit
   */
  public $sizeLimit = self::DEFAULT_SIZE_LIMIT;

  /**
   * @var integer Validation options. Minimum allowed file size to upload. Zero means no limit
   */
  public $minSizeLimit = self::DEFAULT_MIN_SIZE_LIMIT;

  /**
   * @var boolean Validation options. Terminate upload process if an error occurred when file being upload
   */
  public $stopOnFirstInvalidFile = self::DEFAULT_STOP_ON_FIRST_INVALID_FILE;

  /**
   * @var boolean Retry options. When enabled uploader will attempt to upload file again if non-200 server response
   */
  public $enableAuto = self::DEFAULT_ENABLE_AUTO;

  /**
   * @var integer Retry options. The maximum number of times the uploader will attempt to retry a failed upload
   */
  public $maxAutoAttempts = self::DEFAULT_MAX_AUTO_ATTEMPTS;

  /**
   * @var integer Retry options. The number of seconds the uploader will wait in between automatic retry attempts
   */
  public $autoAttemptDelay = self::DEFAULT_AUTO_ATTEMPT_DELAY;

  /**
   * @var string Retry options. If this property is present in the server response and contains a value of true,
   * the uploader will not allow any further retries of this file (manual or automatic).
   */
  public $preventRetryResponseProperty = self::DEFAULT_PREVENT_RETRY_RESPONSE_PROPERTY;

  /**
   * @var array Javascript expressions returning HTML-elements of drop zones
   */
  public $extraDropzones = array();

  /**
   * @var boolean Useful if you do not want all dropzone elements to be hidden. It is expected that some CSS would accompany setting this option to false. You may set this to false if you want to wrap some visible elements, such as the listElement, in a drop zone.
   */
  public $hideDropzones = self::DEFAULT_HIDE_DROPZONES;

  /**
   * @var boolean Set this to true if you are contributing your own drop zone(s) and do not want to use the default one.
   */
  public $disableDefaultDropzone = self::DEFAULT_DISABLE_DEFAULT_DROPZONES;

  /**
   * @var string Label for the file selector button
   */
  public $uploadButton = self::DEFAULT_UPLOAD_BUTTON;

  /**
   * @var string The retry button text
   */
  public $retryButton = self::DEFAULT_RETRY_BUTTON;

  /**
   * @var string The cancel button text (which is more of a link than a button)
   */
  public $cancelButton = self::DEFAULT_CANCEL_BUTTON;

  /**
   * @var string Text that appears next to a failed file item
   */
  public $failUpload = self::DEFAULT_FAIL_UPLOAD;

  /**
   * @var string Text that appears in the drop zone when it is visible
   */
  public $dragZone = self::DEFAULT_DRAG_ZONE;

  /**
   * @var string Text that appears next to a file as it is uploading (if the browser supports the file API)
   */
  public $formatProgress = self::DEFAULT_FORMAT_PROGRESS;

  /**
   * Default waiting for response label
   * @var string
   */
  public $waitingForResponse = self::DEFAULT_WAITING_FOR_RESPONSE;

  /**
   * @var string Valid values are "default" (display the text defined in failUploadText next to each failed file),
   * "none" (don't display any text next to a failed file),
   * and "custom" (display error response text from the server next to the failed file)
   */
  public $mode = self::DEFAULT_MODE;

  /**
   * @var integer The maximum amount of character of text to display next to the file
   */
  public $maxChars = self::DEFAULT_MAX_CHARS;

  /**
   * @var string The property from the server response containing the error text to display next to the failed file
   */
  public $responseProperty = self::DEFAULT_RESPONSE_PROPERTY;

  /**
   * @var boolean If set to true, a tooltip will display the full contents of the error message when the mouse pointer hovers over the failed file
   */
  public $enableTooltip = self::DEFAULT_ENABLE_TOOLTIP;

  /**
   * @var boolean If set to true, a status message will appear next to the file during automatic retry attempts
   */
  public $showAutoRetryNote = self::DEFAULT_SHOW_AUTO_RETRY_NOTE;

  /**
   * @var string The text of the note that will optionally appear next to the file during automatic retry attempts
   */
  public $autoRetryNote = self::DEFAULT_AUTO_RETRY_NOTE;

  /**
   * @var boolean If true, a button/link will appear next to a failed file after all retry attempts have been exhausted
   */
  public $showButton = self::DEFAULT_SHOW_BUTTON;

  /**
   * @var string Javascript expression. onSubmit(String id, String fileName) called when the file is submitted to the uploader
   */
  public $onSubmitCallback;

  /**
   * @var string Javascript expression. onComplete(String id, String fileName, Object responseJSON) called when the file upload has finished
   */
  public $onCompleteCallback;

  /**
   * @var string Javascript expression. onCancel(String id, String fileName) called when the file upload has been cancelled
   */
  public $onCancelCallback;

  /**
   * @var string Javascript expression. onUpload(String id, String fileName) called just before the file upload begins
   */
  public $onUploadCallback;

  /**
   * @var string Javascript expression. onProgress(String id, String fileName, int uploadedBytes, int totalBytes) called during the upload, as it progresses
   */
  public $onProgressCallback;

  /**
   * @var string Javascript expression. onError(String id, String fileName, String errorReason) called whenever an exceptional condition occurs
   */
  public $onErrorCallback;

  /**
   * @var string Javascript expression. onAutoRetry(String id, String fileName, String attemptNumber) called before each automatic retry attempt for a failed file
   */
  public $onAutoRetryCallback;

  /**
   * @var string Javascript expression. onManualRetry(String id, String fileName) called before each manual retry attempt
   */
  public $onManualRetryCallback;

  /**
   * @var string Javascript expression. onValidate(Array fileData) callback is invoked with FileData objects for each of the dropped/selected files
   */
  public $onValidateCallback;
  public $loadingClass = self::DEFAULT_LOADING_CLASS;
  public $buttonClass = self::DEFAULT_BUTTON_CLASS;
  public $dropClass = self::DEFAULT_DROP_CLASS;
  public $dropActiveClass = self::DEFAULT_DROP_ACTIVE_CLASS;
  public $dropDisabledClass = self::DEFAULT_DROP_DISABLED_CLASS;
  public $listClass = self::DEFAULT_LIST_CLASS;
  public $progressBarClass = self::DEFAULT_PROGRESS_BAR_CLASS;
  public $fileClass = self::DEFAULT_FILE_CLASS;
  public $spinnerClass = self::DEFAULT_SPINNER_CLASS;
  public $finishedClass = self::DEFAULT_FINISHED_CLASS;
  public $retryingClass = self::DEFAULT_RETRYING_CLASS;
  public $retryableClass = self::DEFAULT_RETRYABLE_CLASS;
  public $sizeClass = self::DEFAULT_SIZE_CLASS;
  public $cancelClass = self::DEFAULT_CANCEL_CLASS;
  public $retryClass = self::DEFAULT_RETRY_CLASS;
  public $statusTextClass = self::DEFAULT_STATUS_TEXT_CLASS;
  public $successClass = self::DEFAULT_SUCCESS_CLASS;
  public $failClass = self::DEFAULT_FAIL_CLASS;
  public $successIconClass = self::DEFAULT_SUCCESS_ICON_CLASS;
  public $failIconClass = self::DEFAULT_FAIL_ICON_CLASS;
  public $template;
  public $fileTemplate;

  public function init()
  {
    if ($this->onlyRun) {
      return;
    }

    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');

    $cs = Yii::app()->clientScript;
    $cs->registerScriptFile($this->assetsUrl . '/jquery.fineuploader-3.0.' . (YII_DEBUG ? '' : 'min.') . 'js');
    $cs->registerCssFile($this->assetsUrl . '/fineuploader.css');
  }

  public function run()
  {
    if ($this->onlyInit) {
      return;
    }

    if (!$this->id) {
      $this->id = isset($this->htmlOptions['id']) ? $this->htmlOptions['id'] : $this->name;
    }
    $this->htmlOptions['id'] = $this->id;

    if ($this->renderElement) {
      $this->element = $this->id;
    }

    $params = array();

    // required parameters first
    $params['element'] = "js:document.getElementById('{$this->element}')";

    // optional parameters
    if ($this->listElement) {
      $params['listElement'] = "js:document.getElementsById{$this->listElement}";
    }

    if ($this->button) {
      $params['button'] = "js:document.getElementsById('{$this->button}')";
    }

    if ($this->multiple !== self::DEFAULT_MULTIPLE) {
      $params['multiple'] = $this->multiple == true;
    }

    if ($this->maxConnections !== self::DEFAULT_MAX_CONNECTIONS) {
      $params['maxConnections'] = intval($this->maxConnections);
    }

    if ($this->disableCancelForFormUploads !== self::DEFAULT_DISABLE_CANCEL_FOR_FORM_UPLOADS) {
      $params['disableCancelForFormUploads'] = $this->disableCancelForFormUploads == true;
    }

    if ($this->autoUpload) {
      $params['autoUpload'] = $this->autoUpload == true;
    }

    if ($this->debug !== self::DEFAULT_DEBUG) {
      $params['debug'] = $this->debug;
    }

    // request
    $params['request']['endpoint'] = $this->endpoint;

    if ($this->params !== array()) {
      $params['request']['params'] = $this->params;
    }

    if ($this->customHeaders !== array()) {
      $params['request']['customHeaders'] = $this->customHeaders;
    }

    if ($this->forceMultipart !== self::DEFAULT_FORCE_MULTIPART) {
      $params['request']['forceMultipart'] = $this->forceMultipart;
    }

    if ($this->inputName !== self::DEFAULT_INPUT_NAME) {
      $params['request']['inputName'] = $this->inputName;
    }

    // validation
    if ($this->allowedExtensions !== array()) {
      $params['validation']['allowedExtensions'] = $this->allowedExtensions;
    }

    if ($this->acceptFiles) {
      $params['validation']['acceptFiles'] = $this->acceptFiles;
    }

    if ($this->sizeLimit !== self::DEFAULT_SIZE_LIMIT) {
      $params['validation']['sizeLimit'] = $this->sizeLimit;
    }

    if ($this->minSizeLimit !== self::DEFAULT_MIN_SIZE_LIMIT) {
      $params['validation']['minSizeLimit'] = $this->minSizeLimit;
    }

    if ($this->stopOnFirstInvalidFile !== self::DEFAULT_STOP_ON_FIRST_INVALID_FILE) {
      $params['validation']['stopOnFirstInvalidFile'] = $this->stopOnFirstInvalidFile;
    }

    // retry
    if ($this->enableAuto !== self::DEFAULT_ENABLE_AUTO) {
      $params['retry']['enableAuto'] = $this->enableAuto == true;
    }

    if ($this->maxAutoAttempts !== self::DEFAULT_MAX_AUTO_ATTEMPTS) {
      $params['retry']['maxAutoAttempts'] = $this->maxAutoAttempts;
    }

    if ($this->autoAttemptDelay !== self::DEFAULT_AUTO_ATTEMPT_DELAY) {
      $params['retry']['autoAttemptDelay'] = $this->autoAttemptDelay;
    }

    if ($this->preventRetryResponseProperty !== self::DEFAULT_PREVENT_RETRY_RESPONSE_PROPERTY) {
      $params['retry']['preventRetryResponseProperty'] = $this->preventRetryResponseProperty;
    }

    // dragAndDrop
    if ($this->extraDropzones !== array()) {
      $params['dragAndDrop']['extraDropzones'] = $this->extraDropzones;
    }

    if ($this->hideDropzones !== self::DEFAULT_HIDE_DROPZONES) {
      $params['dragAndDrop']['hideDropzones'] = $this->hideDropzones;
    }

    if ($this->disableDefaultDropzone !== self::DEFAULT_DISABLE_DEFAULT_DROPZONES) {
      $params['dragAndDrop']['disableDefaultDropzone'] = $this->disableDefaultDropzone;
    }

    // text
    if ($this->uploadButton !== self::DEFAULT_UPLOAD_BUTTON) {
      $params['text']['uploadButton'] = $this->uploadButton;
    }

    if ($this->cancelButton !== self::DEFAULT_CANCEL_BUTTON) {
      $params['text']['cancelButton'] = $this->cancelButton;
    }

    if ($this->retryButton !== self::DEFAULT_RETRY_BUTTON) {
      $params['text']['retryButton'] = $this->retryButton;
    }

    if ($this->failUpload !== self::DEFAULT_FAIL_UPLOAD) {
      $params['text']['failUpload'] = $this->failUpload;
    }

    if ($this->dragZone !== self::DEFAULT_DRAG_ZONE) {
      $params['text']['dragZone'] = $this->dragZone;
    }

    if ($this->waitingForResponse !== self::DEFAULT_WAITING_FOR_RESPONSE) {
      $params['text']['waitingForResponse'] = $this->waitingForResponse;
    }

    if ($this->formatProgress !== self::DEFAULT_FORMAT_PROGRESS) {
      $params['text']['formatProgress'] = $this->formatProgress;
    }

    // failedUploadTextDisplay
    if ($this->mode !== self::DEFAULT_MODE) {
      $params['failedUploadTextDisplay']['mode'] = $this->mode;
    }

    if ($this->maxChars !== self::DEFAULT_MAX_CHARS) {
      $params['failedUploadTextDisplay']['maxChars'] = intval($this->maxChars);
    }

    if ($this->responseProperty !== self::DEFAULT_RESPONSE_PROPERTY) {
      $params['failedUploadTextDisplay']['responseProperty'] = $this->responseProperty;
    }

    if ($this->enableTooltip !== self::DEFAULT_ENABLE_TOOLTIP) {
      $params['failedUploadTextDisplay']['enableTooltip'] = $this->enableTooltip == true;
    }

    // retry
    if ($this->showAutoRetryNote !== self::DEFAULT_SHOW_AUTO_RETRY_NOTE) {
      $params['retry']['showAutoRetryNote'] = $this->showAutoRetryNote == true;
    }

    if ($this->autoRetryNote !== self::DEFAULT_AUTO_RETRY_NOTE) {
      $params['retry']['autoRetryNote'] = $this->autoRetryNote;
    }

    if ($this->showButton !== self::DEFAULT_SHOW_BUTTON) {
      $params['retry']['showButton'] = $this->showButton == true;
    }

    if ($this->buttonClass !== self::DEFAULT_LOADING_CLASS) {
      $params['classes']['loading'] = $this->loadingClass;
    }

    if ($this->buttonClass !== self::DEFAULT_BUTTON_CLASS) {
      $params['classes']['button'] = $this->buttonClass;
    }

    if ($this->dropClass !== self::DEFAULT_DROP_CLASS) {
      $params['classes']['drop'] = $this->dropClass;
    }

    if ($this->dropActiveClass !== self::DEFAULT_DROP_ACTIVE_CLASS) {
      $params['classes']['dropActive'] = $this->dropActiveClass;
    }

    if ($this->dropDisabledClass !== self::DEFAULT_DROP_DISABLED_CLASS) {
      $params['classes']['dropDisabled'] = $this->dropDisabledClass;
    }

    if ($this->listClass !== self::DEFAULT_LIST_CLASS) {
      $params['classes']['list'] = $this->listClass;
    }

    if ($this->progressBarClass !== self::DEFAULT_PROGRESS_BAR_CLASS) {
      $params['classes']['progressBar'] = $this->progressBarClass;
    }

    if ($this->fileClass !== self::DEFAULT_FILE_CLASS) {
      $params['classes']['file'] = $this->fileClass;
    }

    if ($this->spinnerClass !== self::DEFAULT_SPINNER_CLASS) {
      $params['classes']['spinner'] = $this->spinnerClass;
    }

    if ($this->finishedClass !== self::DEFAULT_FINISHED_CLASS) {
      $params['classes']['finished'] = $this->finishedClass;
    }

    if ($this->retryingClass !== self::DEFAULT_RETRYING_CLASS) {
      $params['classes']['retrying'] = $this->retryingClass;
    }

    if ($this->retryableClass !== self::DEFAULT_RETRYABLE_CLASS) {
      $params['classes']['retryable'] = $this->retryableClass;
    }

    if ($this->sizeClass !== self::DEFAULT_SIZE_CLASS) {
      $params['classes']['size'] = $this->sizeClass;
    }

    if ($this->cancelClass !== self::DEFAULT_CANCEL_CLASS) {
      $params['classes']['cancel'] = $this->cancelClass;
    }

    if ($this->retryClass !== self::DEFAULT_RETRY_CLASS) {
      $params['classes']['retry'] = $this->retryClass;
    }

    if ($this->statusTextClass !== self::DEFAULT_STATUS_TEXT_CLASS) {
      $params['classes']['statusText'] = $this->statusTextClass;
    }

    if ($this->successClass !== self::DEFAULT_SUCCESS_CLASS) {
      $params['classes']['success'] = $this->successClass;
    }

    if ($this->failClass !== self::DEFAULT_FAIL_CLASS) {
      $params['classes']['fail'] = $this->failClass;
    }

    if ($this->successIconClass !== self::DEFAULT_SUCCESS_ICON_CLASS) {
      $params['classes']['successIcon'] = $this->successIconClass;
    }

    if ($this->failIconClass !== self::DEFAULT_FAIL_ICON_CLASS) {
      $params['classes']['failIcon'] = $this->failIconClass;
    }

    if ($this->onSubmitCallback) {
      $params['callbacks']['onSubmit'] = (strpos($this->onSubmitCallback, 'js:') !== 0 ? 'js:' : '') . $this->onSubmitCallback;
    }

    if ($this->onCompleteCallback) {
      $params['callbacks']['onComplete'] = (strpos($this->onCompleteCallback, 'js:') !== 0 ? 'js:' : '') . $this->onCompleteCallback;
    }

    if ($this->onCancelCallback) {
      $params['callbacks']['onCancel'] = (strpos($this->onCancelCallback, 'js:') !== 0 ? 'js:' : '') . $this->onCancelCallback;
    }

    if ($this->onUploadCallback) {
      $params['callbacks']['onUpload'] = (strpos($this->onUploadCallback, 'js:') !== 0 ? 'js:' : '') . $this->onUploadCallback;
    }

    if ($this->onProgressCallback) {
      $params['callbacks']['onProgress'] = (strpos($this->onProgressCallback, 'js:') !== 0 ? 'js:' : '') . $this->onProgressCallback;
    }

    if ($this->onErrorCallback) {
      $params['callbacks']['onError'] = (strpos($this->onErrorCallback, 'js:') !== 0 ? 'js:' : '') . $this->onErrorCallback;
    }

    if ($this->onAutoRetryCallback) {
      $params['callbacks']['onAutoRetry'] = (strpos($this->onAutoRetryCallback, 'js:') !== 0 ? 'js:' : '') . $this->onAutoRetryCallback;
    }

    if ($this->onManualRetryCallback) {
      $params['callbacks']['onManualRetry'] = (strpos($this->onManualRetryCallback, 'js:') !== 0 ? 'js:' : '') . $this->onManualRetryCallback;
    }

    if ($this->onValidateCallback) {
      $params['callbacks']['onValidate'] = (strpos($this->onValidateCallback, 'js:') !== 0 ? 'js:' : '') . $this->onValidateCallback;
    }

    if ($this->template) {
      $params['template'] = $this->template;
    }

    if ($this->fileTemplate) {
      $params['fileTemplate'] = $this->fileTemplate;
    }

    if ($this->renderElement) {
      echo CHtml::tag($this->tag, $this->htmlOptions, '');
    }

    $script = "var " . get_class($this) . '_' . $this->id . " = new qq.FineUploader(" . CJavaScript::encode($params) . ");";
    if ($this->scriptOutput) {
      echo CHtml::tag('script', array(), CHtml::cdata($script));
    }
    else {
      Yii::app()->clientScript->registerScript(get_class($this) . '_' . $this->id, $script, CClientScript::POS_READY);
    }
  }
}
