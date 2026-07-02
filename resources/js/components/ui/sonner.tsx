import type { CSSProperties } from "react"
import { Toaster as Sonner, type ToasterProps } from "sonner"

import { useFlashToast } from "@/hooks/use-flash-toast"

function Toaster({ ...props }: ToasterProps) {
  useFlashToast()

  return (
    <Sonner
      theme="dark"
      className="toaster group"
      position="bottom-right"
      style={
        {
          "--normal-bg": "var(--popover)",
          "--normal-text": "var(--popover-foreground)",
          "--normal-border": "var(--border)",
        } as CSSProperties
      }
      {...props}
    />
  )
}

export { Toaster }
