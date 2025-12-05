export default function Avatar({
  src = "/assets/aktg/avatar.webp",
  alt = "Avatar",
  size = 48,
  rounded = true,
  ring = false,
  dicebear = null,
  className = "",
}) {
  const toQS = (obj) =>
    Object.entries(obj)
      .filter(([, v]) => v !== undefined && v !== null && v !== "")
      .map(([k, v]) =>
        Array.isArray(v) ? `${k}=${encodeURIComponent(v.join(","))}` : `${k}=${encodeURIComponent(v)}`
      )
      .join("&");

  const buildDicebearUrl = (opts) => {
    const base = "https://api.dicebear.com/9.x/thumbs/svg";
    const qs = toQS({
      seed: opts.seed,
      size: opts.size ?? size,
      radius: opts.radius,
      backgroundColor: opts.backgroundColor,
      backgroundType: opts.backgroundType,
      rotate: opts.rotate,
      scale: opts.scale,
      clip: opts.clip,
    });
    return qs ? `${base}?${qs}` : base;
  };

  const computedSrc = dicebear ? buildDicebearUrl(dicebear) : src;
  const wrapCls = `avatar ${ring ? "ring ring-primary" : ""}`;
  const imgCls = `${rounded ? "rounded-full" : "rounded"} ${className}`;

  return (
    <div className={wrapCls} style={{ width: size, height: size }}>
      <img
        src={computedSrc}
        alt={alt}
        className={imgCls}
        style={{ width: size, height: size, objectFit: "cover" }}
        onError={(e) => {
          if (e.currentTarget.src !== src) e.currentTarget.src = src;
        }}
      />
    </div>
  );
}

