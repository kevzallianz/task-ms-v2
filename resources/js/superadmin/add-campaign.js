$(function () {
  const $modal = $('#addCampaignModal');
  const $openBtn = $('#addCampaignBtn');
  const $closeBtn = $('#addCampaignCloseBtn');
  const $cancelBtn = $('#addCampaignCancelBtn');
  const $form = $('#addCampaignForm');
  const $saveBtn = $('#addCampaignSaveBtn');
  const $name = $('#campaignName');
  const $desc = $('#campaignDescription');
  const errorMap = {
    name: $('#campaignNameError'),
    description: $('#campaignDescriptionError'),
  };

  function clearErrors() {
    Object.values(errorMap).forEach(($el) => $el.addClass('hidden').text(''));
  }

  function openModal() {
    clearErrors();
    $name.val('');
    $desc.val('');
    $modal.removeClass('hidden').addClass('flex');
  }

  function closeModal() {
    $modal.addClass('hidden').removeClass('flex');
  }

  $openBtn.on('click', openModal);
  $closeBtn.on('click', closeModal);
  $cancelBtn.on('click', closeModal);

  $form.on('submit', function (e) {
    e.preventDefault();
    clearErrors();

    const url = $form.attr('action');
    const data = {
      _token: $form.find('input[name="_token"]').val(),
      name: $name.val().trim(),
      description: $desc.val().trim(),
    };

    $saveBtn.prop('disabled', true).text('Saving...');

    $.post(url, data)
      .done(function (resp) {
        // Success: close modal and reload to reflect new campaign
        closeModal();
        setTimeout(function () { window.location.reload(); }, 300);
      })
      .fail(function (xhr) {
        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors || {};
          Object.keys(errors).forEach((key) => {
            if (errorMap[key]) {
              errorMap[key].removeClass('hidden').text(errors[key][0]);
            }
          });
        } else {
          alert('Failed to create campaign. Please try again.');
        }
      })
      .always(function () {
        $saveBtn.prop('disabled', false).text('Save Campaign');
      });
  });
});
