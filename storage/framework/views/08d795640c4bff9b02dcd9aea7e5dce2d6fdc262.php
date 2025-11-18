<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['user', 'size' => 'w-10 h-10']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['user', 'size' => 'w-10 h-10']); ?>
<?php foreach (array_filter((['user', 'size' => 'w-10 h-10']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $profileImage = $user->profile_image;
    $initials = strtoupper(substr($user->name, 0, 1));
    $avatarColor = $user->getAvatarColor();
?>

<?php if($profileImage): ?>
    <img src="<?php echo e($profileImage); ?>" alt="<?php echo e($user->name); ?>" class="<?php echo e($size); ?> rounded-full object-cover border-2 border-white shadow-sm">
<?php else: ?>
    <div class="<?php echo e($size); ?> <?php echo e($avatarColor); ?> rounded-full flex items-center justify-center text-white font-bold shadow-sm">
        <?php echo e($initials); ?>

    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ojt360\resources\views/components/user-avatar.blade.php ENDPATH**/ ?>