import {nickColorFor} from "@/lib/user";

export default function Username({name}: {
    name: string;
}) {
    return (
        <span
            style={{
                color: nickColorFor(name),
            }}
        >
            {name}
        </span>
    );
}
