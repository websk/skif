<?php
/**
 * @var $field_name string
 * @var $field_value string
 */
?>

<script>
	var gmbox = gmbox || {};

	gmbox.admin = {};
	gmbox.admin.utils = {};
	gmbox.admin.utils.crud = {};

	gmbox.admin.utils.crud.imageAjaxUploadWidget = function (formField) {
		var currentParent = $("#imageUploadWidget_" + formField);
		var fileInput = $('.file-select');
		var fileInputBtn = $('.select-file-btn');

		var uploadFiles = function (event) {
			event.stopPropagation();
			event.preventDefault();

			var files = currentParent.find('.file-select').prop('files'),
				uploadButton = currentParent.find('.upload-button'),
				data = new FormData(),
				target_folder_select = currentParent.find('.target_folder');

			uploadButton.text("Загружается...").attr('disabled', 'disabled');

			$.each(files, function (key, value) {
				data.append(key, value);
			});

			$.each(target_folder_select, function (key, value) {
				data.append('target_folder', value.value);
			});

			$.ajax({
				url : '/images/upload_to_files',
				type: 'POST',
				data: data,
				cache: false,
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function (response) {
					currentParent.find('.uploaded-image').html('<div class="uploaded-image__preview">' +
					'    <img src="<?=\Skif\CRUD\ImageUploadToFilesWidget\ImageUploadToFilesWidget::getIconFolder()?>/' + response.fileName + '" />' +
					'</div>');
					currentParent.find('.internal-file-name').val(response.fileName);
				},
				error: function (jqXHR, textStatus) {
					alert("Произошла ошибка, попробуйте загрузить еще раз");
				},
				complete: function () {
					uploadButton.text("Загрузить").removeAttr('disabled');
				}
			});
		};

		currentParent.on('click', '.upload-button', uploadFiles);

		fileInputBtn.on('click', function(event) {
			event.preventDefault();
			fileInput.click();
		});
	};
</script>



<div id="imageUploadWidget_<?php echo $field_name ?>">

	<div class="file-upload">
		<div class="row">
			<div class="col-sm-8">
				<input type="file" class="file-select form-control" name="photo" />
				<button type="submit" class="btn btn-default select-file-btn">Выберите файл</button>
			</div>
			<div class="col-sm-4">
				<button type="submit" class="btn btn-default pull-right upload-button">Загрузить</button>
			</div>
		</div>
	</div>

	<div class="uploaded-image">
		<?php if ($field_value) { ?>
			<a class="uploaded-image__preview" target="_blank" href="<?php echo \Skif\Helpers::appendCdnDomain(\Skif\Imagecache::file_directory_path().DIRECTORY_SEPARATOR.$field_value); ?>">
				<img src="<?php echo \Skif\CRUD\ImageUploadToFilesWidget\ImageUploadToFilesWidget::getIconFile($field_value); ?>">
			</a>
		<?php } ?>
	</div>
	<input type="hidden" name="<?php echo $field_name ?>" class="internal-file-name" value="<?php echo $field_value ?>">
</div>

<script>
	(function () {
		gmbox.admin.utils.crud.imageAjaxUploadWidget("<?php echo $field_name ?>");
	})();
</script>